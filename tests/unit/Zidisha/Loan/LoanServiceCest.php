<?php

use Propel\Runtime\Propel;
use Zidisha\Balance\Map\TransactionTableMap;
use Zidisha\Balance\Transaction;
use Zidisha\Currency\Money;
use Zidisha\Loan\Bid;
use Zidisha\Loan\Loan;

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

    public function _before(UnitTester $I)
    {
        $this->loanService = $I->grabService('Zidisha\Loan\LoanService');
        $this->transactionService = $I->grabService('Zidisha\Balance\TransactionService');
    }

    public function _after(UnitTester $I)
    {
    }

    public function testCreateBid(UnitTester $I)
    {
        $loan = \Zidisha\Loan\LoanQuery::create()
            ->findOneById('5');

        $lender = \Zidisha\Lender\LenderQuery::create()
            ->findOneById('203');

        $data = [
            'amount' => '20',
            'interestRate' => '5'
        ];

        $method = new ReflectionMethod($this->loanService, 'createBid');
        $method->setAccessible(true);

        $con = Propel::getWriteConnection(TransactionTableMap::DATABASE_NAME);
        $con->beginTransaction();

        try {
            $method->invoke($this->loanService, $con, $loan, $lender, $data);

            $bid = \Zidisha\Loan\BidQuery::create()
                ->filterByLoan($loan)
                ->filterByLender($lender)
                ->findOne();

            $newBidTransaction = \Zidisha\Balance\TransactionQuery::create()
                ->filterByLoan($loan)
                ->filterByUserId($lender->getId())
                ->findOne();

            verify($bid)->notEmpty();
            verify($bid->getInterestRate())->equals($data['interestRate']);
            verify($bid->getBidAmount())->equals(Money::create($data['amount']));
            verify($newBidTransaction)->notEmpty();
            verify($newBidTransaction->getAmount())->equals(Money::create($data['amount'])->multiply(-1));
        } catch (\Exception $e) {
            $con->rollBack();
            throw $e;
        }

        $con->rollBack();
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
            $newBid->setBidDate(new \DateTime());
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
        $con = Propel::getWriteConnection(TransactionTableMap::DATABASE_NAME);
        $con->beginTransaction();


        $borrower = \Zidisha\Borrower\BorrowerQuery::create()
            ->findOneById(12);

        $borrowerId = $borrower->getId();
        $data = [
            'categoryId' => '7',
            'amount' => '798097',
            'summary' => 'suasdasd',
            'proposal' => 'asdasda',
            'installmentAmount' => '2312',
            'installmentDay' => '1',
            'amountRaised' => 0,
            'interestRate' => 10
        ];

        try {
            $this->loanService->applyForLoan($borrower, $data);

            $LoanCount = \Zidisha\Loan\LoanQuery::create()
                ->filterByStatus(Loan::OPEN)
                ->filterByBorrowerId($borrowerId)->count();


            verify($LoanCount)->greaterThan(0);
        } catch (\Exception $e){
            $con->rollBack();
            throw $e;
        }

        $con->rollBack();
    }

    public function testChangeLoanStage(UnitTester $I)
    {
        $method = new ReflectionMethod($this->loanService, 'changeLoanStage');
        $method->setAccessible(true);

        $loan = \Zidisha\Loan\LoanQuery::create()
            ->findOneById('1');

        $con = Propel::getWriteConnection(TransactionTableMap::DATABASE_NAME);
        $con->beginTransaction();

        try {
            $method->invoke($this->loanService, $con, $loan, \Zidisha\Loan\Loan::OPEN, \Zidisha\Loan\Loan::FUNDED);

            $recordCount = \Zidisha\Loan\StageQuery::create()
                ->filterByStatus(Loan::FUNDED)
                ->findByLoanId($loan->getId())
                ->count();

            verify($recordCount)->equals(1);
        } catch (\Exception $e) {
            throw $e;
        }
        $con->rollBack();
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

        $con = Propel::getWriteConnection(TransactionTableMap::DATABASE_NAME);
        $con->beginTransaction();

        $loan = \Zidisha\Loan\LoanQuery::create()
            ->findOneById(5);

        $lender1 = \Zidisha\Lender\LenderQuery::create()
            ->findOneById(203);

        $lender2 = \Zidisha\Lender\LenderQuery::create()
            ->findOneById(204);

        $lender3 = \Zidisha\Lender\LenderQuery::create()
            ->findOneById(205);

        try {
            $this->transactionService->addPlaceBidTransaction($con, Money::create(50), $loan, $lender1);
            $this->transactionService->addPlaceBidTransaction($con, Money::create(30), $loan, $lender2);
            $this->transactionService->addPlaceBidTransaction($con, Money::create(40), $loan, $lender3);
            $this->transactionService->addOutBidTransaction($con, Money::create(20), $loan, $lender1);
            $this->transactionService->addUpdateBidTransaction($con, Money::create(10), $loan, $lender2);
            $this->transactionService->addOutBidTransaction($con, Money::create(10), $loan, $lender1);

            $refunds = $method->invoke($this->loanService, $con, $loan, Loan::EXPIRED);

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
            $con->rollBack();
        } catch (\Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }
}
