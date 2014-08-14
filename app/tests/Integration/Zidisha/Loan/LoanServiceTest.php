<?php

namespace Integration\Zidisha\Loan;

use ReflectionMethod;
use Zidisha\Admin\Setting;
use Zidisha\Balance\Transaction;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Currency\Money;
use Zidisha\Lender\Lender;
use Zidisha\Loan\LenderRefund;
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
            ->size(4)
            ->generate();

        $this->borrowers = \Zidisha\Generate\BorrowerGenerator::create()
            ->size(3)
            ->generate();

        $this->loan = \Zidisha\Generate\LoanGenerator::create()
            ->amount(50)
            ->generateOne();
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
            'interestRate'       => '0',
            'amount'             => '10',
            'lenderInviteCredit' => true,
            'acceptedAmount'     => '10',
            'raisedUsdAmount'    => '30',
        ]);
        $this->assertBid($loan, $this->lenders[2], [
            'interestRate'    => '5',
            'amount'          => '30',
            'acceptedAmount'  => '30',
            'raisedUsdAmount' => '50',
        ]);
        $this->assertBid($loan, $this->lenders[1], [
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
    
    protected function assertBid(Loan $loan, Lender $lender, $data)
    {
        $bid = $this->loanService->placeBid($loan, $lender, $data);

        $this->assertNotEmpty($bid);
        $this->assertEquals($data['interestRate'], $bid->getInterestRate());
        $this->assertEquals(Money::create($data['amount']), $bid->getBidAmount());

        $placeBidTransaction = TransactionQuery::create()
            ->filterByLoanBidId($bid->getId())
            ->filterByUserId($lender->getId())
            ->filterByType(Transaction::LOAN_BID)
            ->filterBySubType(Transaction::PLACE_BID)
            ->findOne();
        
        $acceptedAmount = Money::create($data['acceptedAmount']);
        
        if ($acceptedAmount->isZero()) {
            $this->assertEmpty($placeBidTransaction);
        } else {
            $this->assertNotEmpty($placeBidTransaction);
            $this->assertEquals($acceptedAmount, $placeBidTransaction->getAmount()->multiply(-1));
        }

        if ($bid->getLenderInviteCredit()) {
            $inviteTransaction = \Zidisha\Balance\InviteTransactionQuery::create()
                ->filterByLoanBidId($bid->getId())
                ->filterByLenderId($lender->getId())
                ->filterByType(Transaction::LENDER_INVITE_REDEEM)
                ->findOne();

            $inviteRedeemTransaction = TransactionQuery::create()
                ->filterByLoanBidId($bid->getId())
                ->filterByUserId($lender->getId())
                ->filterByType(Transaction::LENDER_INVITE_CREDIT)
                ->filterBySubType(Transaction::LENDER_INVITE_REDEEM)
                ->findOne();

            $YCInviteRedeemTransaction = TransactionQuery::create()
                ->filterByLoanBidId($bid->getId())
                ->filterByUserId(Setting::get('site.YCAccountId'))
                ->filterByType(Transaction::LENDER_INVITE_CREDIT)
                ->filterBySubType(Transaction::LENDER_INVITE_REDEEM)
                ->findOne();

            if ($acceptedAmount->isZero()) {
                $this->assertEmpty($inviteTransaction);
                $this->assertEmpty($inviteRedeemTransaction);
                $this->assertEmpty($YCInviteRedeemTransaction);
            } else {
                $this->assertEquals($acceptedAmount, $inviteTransaction->getAmount()->multiply(-1));
                $this->assertEquals($acceptedAmount, $inviteRedeemTransaction->getAmount());
                $this->assertEquals($acceptedAmount, $YCInviteRedeemTransaction->getAmount()->multiply(-1));
            }
        }

        $raisedUsdAmount = Money::create($data['raisedUsdAmount']);
        $this->assertEquals($raisedUsdAmount, $loan->getRaisedUsdAmount());
        $this->assertEquals(round($loan->getRaisedUsdAmount()->ratio($loan->getUsdAmount()) * 100, 2), $loan->getRaisedPercentage());
    }

    public function testExpireLoan()
    {
        /** @var Loan $loan */
        $loan = $this->loan;

        /** @var Lender $lender1 */
        $lender1 = $this->lenders[0];
        /** @var Lender $lender2 */
        $lender2 = $this->lenders[1];
        /** @var Lender $lender3 */
        $lender3 = $this->lenders[2];
        /** @var Lender $lender4 */
        $lender4 = $this->lenders[3];
        
        $bids = [
            ['lender' => $lender1, 'amount' => '10', 'interestRate' => 10],
            ['lender' => $lender2, 'amount' => '20', 'interestRate' => 0, 'lenderInviteCredit' => true],
            ['lender' => $lender3, 'amount' => '30', 'interestRate' => 15],
            ['lender' => $lender2, 'amount' => '5',  'interestRate' => 3],
            ['lender' => $lender4, 'amount' => '5',  'interestRate' => 0, 'lenderInviteCredit' => true],
        ];
        
        $refunds = [
            ['lender' => $lender1, 'amount' => '10', 'lenderInviteCredit' => '0'],
            ['lender' => $lender2, 'amount' => '5',  'lenderInviteCredit' => '20'],
            ['lender' => $lender3, 'amount' => '10', 'lenderInviteCredit' => '0'],
            ['lender' => $lender4, 'amount' => '0',  'lenderInviteCredit' => '5'],
        ];
        
        foreach ($bids as $bid) {
            $this->loanService->placeBid($loan, $bid['lender'], $bid);  
        }

        $lenderRefunds = $this->loanService->expireLoan($loan);
        
        $this->assertEquals(Loan::EXPIRED, $loan->getStatus());
        $this->assertEquals(Loan::NO_LOAN, $loan->getBorrower()->getLoanStatus());
        $this->assertNull($loan->getBorrower()->getActiveLoanId());
        
        $this->assertLenderRefunds($refunds, $lenderRefunds);
    }

    public function testCancelLoan()
    {
        /** @var Loan $loan */
        $loan = $this->loan;

        /** @var Lender $lender1 */
        $lender1 = $this->lenders[0];
        /** @var Lender $lender2 */
        $lender2 = $this->lenders[1];
        /** @var Lender $lender3 */
        $lender3 = $this->lenders[2];
        /** @var Lender $lender4 */
        $lender4 = $this->lenders[3];

        $bids = [
            ['lender' => $lender1, 'amount' => '20', 'interestRate' => 5],
            ['lender' => $lender2, 'amount' => '20', 'interestRate' => 10],
            ['lender' => $lender3, 'amount' => '10', 'interestRate' => 0, 'lenderInviteCredit' => true],
            ['lender' => $lender2, 'amount' => '10', 'interestRate' => 3],
            ['lender' => $lender1, 'amount' => '8',  'interestRate' => 0, 'lenderInviteCredit' => true],
        ];

        $refunds = [
            ['lender' => $lender1, 'amount' => '20', 'lenderInviteCredit' => '8'],
            ['lender' => $lender2, 'amount' => '12', 'lenderInviteCredit' => '0'],
            ['lender' => $lender3, 'amount' => '0',  'lenderInviteCredit' => '10'],
        ];

        foreach ($bids as $bid) {
            $this->loanService->placeBid($loan, $bid['lender'], $bid);
        }

        $lenderRefunds = $this->loanService->cancelLoan($loan);
        
        $this->assertEquals(Loan::CANCELED, $loan->getStatus());
        $this->assertEquals(Loan::NO_LOAN, $loan->getBorrower()->getLoanStatus());
        $this->assertNull($loan->getBorrower()->getActiveLoanId());
        
        $this->assertLenderRefunds($refunds, $lenderRefunds, Loan::CANCELED);
    }

    protected function assertLenderRefunds($refunds, $lenderRefunds, $status = Loan::EXPIRED)
    {
        $totalLenderInviteCredit = Money::create(0);
        $transactionType = $status == Loan::CANCELED ? Transaction::LOAN_BID_CANCELED : Transaction::LOAN_BID_EXPIRED;

        foreach ($refunds as $refund) {
            /** @var Lender $lender */
            $lender = $refund['lender'];
            /** @var LenderRefund $refundLender */
            $refundLender = $lenderRefunds[$lender->getId()];
            $amount = Money::create($refund['amount']);
            $lenderInviteCredit = Money::create($refund['lenderInviteCredit']);

            $this->assertEquals($amount, $refundLender->getAmount(), 'amount ' . $lender->getFirstName());
            $this->assertEquals($lenderInviteCredit, $refundLender->getLenderInviteCredit(), 'lenderInviteCredit ' . $lender->getFirstName());

            $lenderTransaction = TransactionQuery::create()
                ->filterByUserId($lender->getId())
                ->filterByType(Transaction::LOAN_OUTBID)
                ->filterBySubType($transactionType)
                ->findOne();

            if ($amount->isPositive()) {
                $this->assertEquals($amount, $lenderTransaction->getAmount(), 'transaction ' . $lender->getFirstName());
            } else {
                $this->assertEmpty($lenderTransaction, 'transaction ' . $lender->getFirstName());
            }

            $totalLenderInviteCredit = $totalLenderInviteCredit->add($lenderInviteCredit);
        }

        $YCTransaction = TransactionQuery::create()
            ->filterByUserId(Setting::get('site.YCAccountId'))
            ->filterByType(Transaction::LOAN_OUTBID)
            ->filterBySubType($transactionType)
            ->findOne();

        if ($totalLenderInviteCredit->isPositive()) {
            $this->assertEquals($totalLenderInviteCredit, $YCTransaction->getAmount());
        } else {
            $this->assertEmpty($YCTransaction);
        }
    }
}