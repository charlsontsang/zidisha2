<?php

namespace Integration\Zidisha\Loan;

use ReflectionMethod;
use Zidisha\Balance\Transaction;
use Zidisha\Currency\Money;
use Zidisha\Lender\Lender;
use Zidisha\Loan\Bid;
use Zidisha\Loan\Loan;

class LoanServiceTest extends \IntegrationTestCase
{

    protected $lenders;
    protected $borrowers;
    /** @var  Loan $loan */
    protected $loan;
    
    /**
     * @var \Zidisha\Loan\LoanService
     */
    private $loanService;

    /**
     * @var \Zidisha\Balance\TransactionService
     */
    private $transactionService;

    public function setUp()
    {
        parent::setUp();
        
        $this->loanService = $this->app->make('Zidisha\Loan\LoanService');
        $this->transactionService = $this->app->make('Zidisha\Balance\TransactionService');

        $this->lenders = \Zidisha\Generate\LenderGenerator::create()
            ->size(3)
            ->generate();

        $this->borrowers = \Zidisha\Generate\BorrowerGenerator::create()
            ->size(3)
            ->generate();

        $this->loan = \Zidisha\Generate\LoanGenerator::create()
            ->amount(50)
            ->generateOne();
    }

    public function testPlaceBid()
    {
        /** @var Loan $loan */
        $loan = $this->loan;

        $this->assertBid($loan, $this->lenders[0], [
            'interestRate'    => '10',
            'amount'          => '20',
            'acceptedAmount'  => '20',
            'raisedUsdAmount' => '20',
        ]);
        $this->assertBid($loan, $this->lenders[1], [
            'interestRate'    => '5',
            'amount'          => '40',
            'acceptedAmount'  => '40',
            'raisedUsdAmount' => '50',
        ]);
        // Outbid
        $this->assertBid($loan, $this->lenders[2], [
            'interestRate'    => '7',
            'amount'          => '20',
            'acceptedAmount'  => '10',
            'raisedUsdAmount' => '50',
        ]);
        $this->assertBid($loan, $this->lenders[0], [
            'interestRate'    => '13',
            'amount'          => '20',
            'acceptedAmount'  => '0',
            'raisedUsdAmount' => '50',
        ]);
    }

    public function testApplyForLoan()
    {
        $borrower = \Zidisha\Borrower\BorrowerQuery::create()
            ->filterByLoanStatus(Loan::NO_LOAN)
            ->findOne();

        $borrowerId = $borrower->getId();
        $data = [
            'categoryId'        => '7',
            'amount'            => '798097',
            'summary'           => 'suasdasd',
            'proposal'          => 'asdasda',
            'installmentAmount' => '2312',
            'installmentDay'    => '1',
        ];

        $oldLoanCount = \Zidisha\Loan\LoanQuery::create()
            ->filterByStatus(Loan::OPEN)
            ->filterByBorrowerId($borrowerId)->count();

        $loan = $this->loanService->applyForLoan($borrower, $data);

        $this->assertEquals(Loan::OPEN, $borrower->getLoanStatus());
        $this->assertEquals($loan->getId(), $borrower->getActiveLoanId());
        $this->assertEquals(Money::create(0, $loan->getCurrencyCode()), $loan->getPaidAmount());
        $this->assertEquals(0, $loan->getPaidPercentage());

        $loanCount = \Zidisha\Loan\LoanQuery::create()
            ->filterByStatus(Loan::OPEN)
            ->filterByBorrowerId($borrowerId)->count();

        $this->assertEquals($oldLoanCount + 1, $loanCount);
    }

    public function testChangeLoanStage()
    {
        $method = new ReflectionMethod($this->loanService, 'changeLoanStage');
        $method->setAccessible(true);

        $loan = $this->loan;

        $method->invoke($this->loanService, $this->con, $loan, \Zidisha\Loan\Loan::OPEN, \Zidisha\Loan\Loan::FUNDED);

        $recordCount = \Zidisha\Loan\StageQuery::create()
            ->filterByStatus(Loan::FUNDED)
            ->findByLoanId($loan->getId())
            ->count();

        $this->assertEquals(1, $recordCount);
    }
    
    protected function assertBid(Loan $loan, Lender $lender, $data)
    {
        $bid = $this->loanService->placeBid($loan, $lender, $data);

        $newBidTransaction = \Zidisha\Balance\TransactionQuery::create()
            ->filterByLoanBidId($bid->getId())
            ->filterByUserId($lender->getId())
            ->filterByType(Transaction::LOAN_BID)
            ->filterBySubType(Transaction::PLACE_BID)
            ->findOne();

        $this->assertNotEmpty($bid);
        $this->assertEquals($data['interestRate'], $bid->getInterestRate());
        $this->assertEquals(Money::create($data['amount']), $bid->getBidAmount());

        if (!$data['acceptedAmount']) {
            $this->assertEmpty($newBidTransaction);
        } else {
            $this->assertNotEmpty($newBidTransaction);
            $this->assertEquals(Money::create($data['acceptedAmount']), $newBidTransaction->getAmount()->multiply(-1));
        }

        $raisedUsdAmount = Money::create($data['raisedUsdAmount']);
        $this->assertEquals($raisedUsdAmount, $loan->getRaisedUsdAmount());
        $this->assertEquals(round($loan->getRaisedUsdAmount()->ratio($loan->getUsdAmount()) * 100, 2), $loan->getRaisedPercentage());
    }

    public function testRefundLenders()
    {
        $method = new ReflectionMethod($this->loanService, 'refundLenders');
        $method->setAccessible(true);

        /** @var Loan $loan */
        $loan = $this->loan;

        /** @var Lender $lender1 */
        $lender1 = $this->lenders[0];
        /** @var Lender $lender2 */
        $lender2 = $this->lenders[1];
        /** @var Lender $lender3 */
        $lender3 = $this->lenders[2];
        
        $bid1 = $this->loanService->placeBid($loan, $lender1, ['amount' => '10', 'interestRate' => 10]);
        $bid2 = $this->loanService->placeBid($loan, $lender2, ['amount' => '20', 'interestRate' => 5]);
        $bid3 = $this->loanService->placeBid($loan, $lender3, ['amount' => '30', 'interestRate' => 15]);
        $bid4 = $this->loanService->placeBid($loan, $lender1, ['amount' => '5', 'interestRate' => 3]);

        //$this->loanService->editBid($bid2, ['amount' => '30', 'interestRate' => 5]); TODO

        $refunds = $this->loanService->expireLoan($loan);

        $this->assertEquals(Money::create(15), $refunds[$lender1->getId()]->getAmount());
        $this->assertEquals(Money::create(20), $refunds[$lender2->getId()]->getAmount());
        $this->assertEquals(Money::create(15), $refunds[$lender3->getId()]->getAmount());

        $lender1VerifyRefund = \Zidisha\Balance\TransactionQuery::create()
            ->filterByUserId($lender1->getId())
            ->filterByType(Transaction::LOAN_OUTBID)
            ->filterBySubType(Transaction::LOAN_BID_EXPIRED)
            ->count();

        $lender2VerifyRefund = \Zidisha\Balance\TransactionQuery::create()
            ->filterByUserId($lender2->getId())
            ->filterByType(Transaction::LOAN_OUTBID)
            ->filterBySubType(Transaction::LOAN_BID_EXPIRED)
            ->count();

        $lender3VerifyRefund = \Zidisha\Balance\TransactionQuery::create()
            ->filterByUserId($lender1->getId())
            ->filterByType(Transaction::LOAN_OUTBID)
            ->filterBySubType(Transaction::LOAN_BID_EXPIRED)
            ->count();

        $this->assertEquals(1, $lender1VerifyRefund);
        $this->assertEquals(1, $lender2VerifyRefund);
        $this->assertEquals(1, $lender3VerifyRefund);
    }
}
