<?php

use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Propel;
use Zidisha\Balance\Transaction;
use Zidisha\Currency\Money;
use Zidisha\Lender\Lender;
use Zidisha\Loan\Bid;
use Zidisha\Loan\Loan;
use Zidisha\User\Map\UserTableMap;

class LoanServiceCest
{
    /**
     * @var Zidisha\Loan\LoanService
     */
    private $loanService;

    /**
     * @var Zidisha\Balance\TransactionService
     */
    private $transactionService;

    /**
     * @var ConnectionInterface
     */
    protected $con;

    public function __construct()
    {
        $this->con = Propel::getWriteConnection(UserTableMap::DATABASE_NAME);
    }

    public function _before(UnitTester $I)
    {
        $this->loanService = $I->grabService('Zidisha\Loan\LoanService');
        $this->transactionService = $I->grabService('Zidisha\Balance\TransactionService');

        $this->con->beginTransaction();
    }

    public function _after(UnitTester $I)
    {
        $this->con->rollBack();
    }

    public function testCreateBid(UnitTester $I)
    {
        $loan = \Zidisha\Loan\LoanQuery::create()
            ->findOneById('100');
        $loan->setAmount(Money::create(50));

        $lender1 = \Zidisha\Lender\LenderQuery::create()
            ->findOneById('203');
        $lender2 = \Zidisha\Lender\LenderQuery::create()
            ->findOneById('204');
        $lender3 = \Zidisha\Lender\LenderQuery::create()
            ->findOneById('205');

        $this->verifyBid($loan, $lender1, [
            'interestRate'   => '10',
            'amount'         => '20',
            'acceptedAmount' => '20',
        ]);
        $this->verifyBid($loan, $lender2, [
            'interestRate'   => '5',
            'amount'         => '40',
            'acceptedAmount' => '40',
        ]);
        $this->verifyBid($loan, $lender3, [
            'interestRate'   => '7',
            'amount'         => '20',
            'acceptedAmount' => '10',
        ]);
    }

    protected function verifyBid(Loan $loan, Lender $lender, $data)
    {
        $method = new ReflectionMethod($this->loanService, 'createBid');
        $method->setAccessible(true);
        $method->invoke($this->loanService, $this->con, $loan, $lender, $data);

        $bid = \Zidisha\Loan\BidQuery::create()
            ->filterByLoan($loan)
            ->filterByLender($lender)
            ->orderById('desc')
            ->findOne();

        $newBidTransaction = \Zidisha\Balance\TransactionQuery::create()
            ->filterByLoan($loan)
            ->filterByUserId($lender->getId())
            ->orderById('desc')
            ->findOne();

        verify($bid)->notEmpty();
        verify($bid->getInterestRate())->equals($data['interestRate']);
        verify($bid->getBidAmount())->equals(Money::create($data['amount']));
        verify($newBidTransaction)->notEmpty();
        verify($newBidTransaction->getAmount())->equals(Money::create($data['acceptedAmount'])->multiply(-1));        
    }

    public function testGetAcceptedBids(UnitTester $I)
    {
        // id => ['interestRate', 'bidAmount', 'acceptedAmount']

        $this->verifyAcceptedBids(
            [
                '1' => ['3', '50', '50'],
                '20' => ['4', '20', '20'],
                '8' => ['10', '100', '100'],
            ],
            200
        );

        $this->verifyAcceptedBids(
            [
                '8' => ['1', '23', '23'],
                '1' => ['3', '50', '50'],
                '20' => ['4', '20', '20'],
                '27' => ['5', '34', '34'],
                '45' => ['6', '34', '34'],
                '65' => ['8', '75', '39'],
                '55' => ['9', '55', '0'],
                '88' => ['11', '95', '0'],
                '98' => ['15', '85', '0'],
            ],
            200
        );

        $this->verifyAcceptedBids(
            [
                '1' => ['3', '10', '10'],
            ],
            120
        );
    }

    private function generateBid(array $bidData)
    {
        $bids = [];

        foreach ($bidData as $id => $bid) {
            $newBid = new Bid();
            $newBid->setInterestRate($bid[0]);
            $newBid->setBidAmount(Money::create($bid[1]));
            $newBid->setBidAt(new \DateTime());
            $newBid->setId($id);
            $bids[$id] = $newBid;
        }

        return $bids;
    }

    /**
     * @param $bidData
     * @param $amount
     */
    protected function verifyAcceptedBids($bidData, $amount)
    {
        $acceptedBids = $this->getAcceptedBids($bidData, $amount);

        foreach ($bidData as $id => $data) {
            verify($acceptedBids)->hasKey($id);
            verify($acceptedBids[$id]['acceptedAmount'])->equals(Money::create($data[2]));
        }
    }

    public function testApplyForLoan(UnitTester $I)
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

        verify($borrower->getLoanStatus())->equals(Loan::OPEN);
        verify($borrower->getActiveLoanId())->equals($loan->getId());
        
        $loanCount = \Zidisha\Loan\LoanQuery::create()
            ->filterByStatus(Loan::OPEN)
            ->filterByBorrowerId($borrowerId)->count();
        
        verify($loanCount)->equals($oldLoanCount + 1);
    }

    public function testChangeLoanStage(UnitTester $I)
    {
        $method = new ReflectionMethod($this->loanService, 'changeLoanStage');
        $method->setAccessible(true);

        $loan = \Zidisha\Loan\LoanQuery::create()
            ->findOneById('1');

        $method->invoke($this->loanService, $this->con, $loan, \Zidisha\Loan\Loan::OPEN, \Zidisha\Loan\Loan::FUNDED);

        $recordCount = \Zidisha\Loan\StageQuery::create()
            ->filterByStatus(Loan::FUNDED)
            ->findByLoanId($loan->getId())
            ->count();

        verify($recordCount)->equals(1);

    }

    public function testGetChangedBids(UnitTester $I)
    {
        $this->verifyChangedBids(
            [
                '1' => ['3', '50', '50'],
                '20' => ['4', '20', '20'],
                '8' => ['10', '100', '100'],
            ],
            [
                '1' => ['3', '50', '50'],
                '20' => ['4', '20', '20'],
                '8' => ['10', '100', '100'],
                '11' => ['2', '15', '15'],
            ],
            200,
            [
                '11' => [
                    'acceptedAmount' => '15',
                    'changedAmount' => '15',
                    'type' => 'place_bid'
                ],
            ]
        );

        $this->verifyChangedBids(
            [
                '1' => ['3', '50', '50'],
                '20' => ['4', '20', '20'],
                '8' => ['10', '100', '100'],
            ],
            [
                '1' => ['3', '50', '50'],
                '20' => ['4', '20', '20'],
                '11' => ['5', '100', '100'],
                '8' => ['10', '100', '30'],
            ],
            200,
            [
                '8' => [
                    'acceptedAmount' => '30',
                    'changedAmount' => '70',
                    'type' => 'out_bid'
                ],
                '11' => [
                    'acceptedAmount' => '100',
                    'changedAmount' => '100',
                    'type' => 'place_bid'
                ],
            ]
        );

        $this->verifyChangedBids(
            [
                '1' => ['3', '50', '50'],
                '20' => ['4', '20', '20'],
                '8' => ['10', '50', '30'],
            ],
            [
                '8' => ['1', '40', '40'],
                '1' => ['3', '50', '50'],
                '20' => ['4', '20', '10'],
            ],
            100,
            [
                '8' => [
                    'acceptedAmount' => '40',
                    'changedAmount' => '10',
                    'type' => 'update_bid'
                ],
                '20' => [
                    'acceptedAmount' => '10',
                    'changedAmount' => '10',
                    'type' => 'out_bid'
                ],
            ]
        );

        $this->verifyChangedBids(
            [
                '1' => ['6', '7', '7'],
                '2' => ['8', '0', '0'],
                '8' => ['14', '22', '22'],
            ],
            [
                '4' => ['5', '20', '20'],
                '1' => ['6', '7', '7'],
                '2' => ['8', '0', '0'],
                '8' => ['14', '22', '22'],
            ],
            65,
            [
                '4' => [
                    'acceptedAmount' => '20',
                    'changedAmount' => '20',
                    'type' => 'place_bid'
                ],
            ]
        );
    }

    /**
     * @param $bidData
     * @param $amount
     * @return mixed
     */
    protected function getAcceptedBids($bidData, $amount)
    {
        $method = new ReflectionMethod($this->loanService, 'getAcceptedBids');
        $method->setAccessible(true);

        $bids = $this->generateBid($bidData);

        $acceptedBids = $method->invoke($this->loanService, $bids, Money::create($amount));
        return $acceptedBids;
    }

    private function verifyChangedBids($oldBids, $newBids, $LoanAmount, $verify)
    {
        $oldAcceptedBids = $this->getAcceptedBids($oldBids, $LoanAmount);
        $newAcceptedBids = $this->getAcceptedBids($newBids, $LoanAmount);

        $method = new ReflectionMethod($this->loanService, 'getChangedBids');
        $method->setAccessible(true);

        $changedBids = $method->invoke($this->loanService, $oldAcceptedBids, $newAcceptedBids);

        verify(count($changedBids))->equals(count($verify));

        foreach ($verify as $id => $haveKeys) {
            verify($changedBids[$id]['acceptedAmount'])->equals(Money::create($haveKeys['acceptedAmount']));
            verify($changedBids[$id]['changedAmount'])->equals(Money::create($haveKeys['changedAmount']));
            verify($changedBids[$id]['type'])->equals($haveKeys['type']);
        }
    }

    public function testRefundLenders(UnitTester $I)
    {
        $method = new ReflectionMethod($this->loanService, 'refundLenders');
        $method->setAccessible(true);

        $loan = \Zidisha\Loan\LoanQuery::create()
            ->findOneById(5);

        $lender1 = \Zidisha\Lender\LenderQuery::create()
            ->findOneById(203);

        $lender2 = \Zidisha\Lender\LenderQuery::create()
            ->findOneById(204);

        $lender3 = \Zidisha\Lender\LenderQuery::create()
            ->findOneById(205);

        $this->transactionService->addPlaceBidTransaction($this->con, Money::create(50), $loan, $lender1);
        $this->transactionService->addPlaceBidTransaction($this->con, Money::create(30), $loan, $lender2);
        $this->transactionService->addPlaceBidTransaction($this->con, Money::create(40), $loan, $lender3);
        $this->transactionService->addOutBidTransaction($this->con, Money::create(20), $loan, $lender1);
        $this->transactionService->addUpdateBidTransaction($this->con, Money::create(10), $loan, $lender2);
        $this->transactionService->addOutBidTransaction($this->con, Money::create(10), $loan, $lender1);

        $refunds = $method->invoke($this->loanService, $this->con, $loan, Loan::EXPIRED);

        verify($refunds[$lender1->getId()]['refundAmount'])->equals(Money::create(20));
        verify($refunds[$lender2->getId()]['refundAmount'])->equals(Money::create(40));
        verify($refunds[$lender3->getId()]['refundAmount'])->equals(Money::create(40));

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

        verify($lender1VerifyRefund)->equals(1);
        verify($lender2VerifyRefund)->equals(1);
        verify($lender3VerifyRefund)->equals(1);
    }
}
