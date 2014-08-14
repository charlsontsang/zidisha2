<?php

namespace Unit\Zidisha\Loan;

use ReflectionMethod;
use Zidisha\Currency\Money;
use Zidisha\Loan\Bid;

class LoanServiceTest extends \TestCase
{
    /**
     * @var \Zidisha\Loan\LoanService
     */
    private $loanService;

    public function setUp()
    {
        parent::setUp();
        $this->loanService = $this->app->make('Zidisha\Loan\LoanService');
    }

    public function testGetAcceptedBids()
    {
        // id => ['interestRate', 'bidAmount', 'acceptedAmount']

        $this->assertAcceptedBids(
            [
                '1'  => ['3', '50', '50'],
                '20' => ['4', '20', '20'],
                '8'  => ['10', '100', '100'],
            ],
            200
        );

        $this->assertAcceptedBids(
            [
                '8'  => ['1', '23', '23'],
                '1'  => ['3', '50', '50'],
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

        $this->assertAcceptedBids(
            [
                '1' => ['3', '10', '10'],
            ],
            120
        );
    }

    protected function generateBid(array $bidData)
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
    protected function assertAcceptedBids($bidData, $amount)
    {
        $acceptedBids = $this->getAcceptedBids($bidData, $amount);

        foreach ($bidData as $id => $data) {
            $this->assertArrayHasKey($id, $acceptedBids);
            $this->assertEquals(Money::create($data[2]), $acceptedBids[$id]['acceptedAmount']);
        }
    }

    public function testGetChangedBids()
    {
        $this->assertChangedBids(
            [
                '1'  => ['3', '50', '50'],
                '20' => ['4', '20', '20'],
                '8'  => ['10', '100', '100'],
            ],
            [
                '1'  => ['3', '50', '50'],
                '20' => ['4', '20', '20'],
                '8'  => ['10', '100', '100'],
                '11' => ['2', '15', '15'],
            ],
            200,
            [
                '11' => [
                    'acceptedAmount' => '15',
                    'changedAmount'  => '15',
                    'type'           => 'place_bid'
                ],
            ]
        );

        $this->assertChangedBids(
            [
                '1'  => ['3', '50', '50'],
                '20' => ['4', '20', '20'],
                '8'  => ['10', '100', '100'],
            ],
            [
                '1'  => ['3', '50', '50'],
                '20' => ['4', '20', '20'],
                '11' => ['5', '100', '100'],
                '8'  => ['10', '100', '30'],
            ],
            200,
            [
                '8'  => [
                    'acceptedAmount' => '30',
                    'changedAmount'  => '70',
                    'type'           => 'out_bid'
                ],
                '11' => [
                    'acceptedAmount' => '100',
                    'changedAmount'  => '100',
                    'type'           => 'place_bid'
                ],
            ]
        );

        $this->assertChangedBids(
            [
                '1'  => ['3', '50', '50'],
                '20' => ['4', '20', '20'],
                '8'  => ['10', '50', '30'],
            ],
            [
                '8'  => ['1', '40', '40'],
                '1'  => ['3', '50', '50'],
                '20' => ['4', '20', '10'],
            ],
            100,
            [
                '8'  => [
                    'acceptedAmount' => '40',
                    'changedAmount'  => '10',
                    'type'           => 'update_bid'
                ],
                '20' => [
                    'acceptedAmount' => '10',
                    'changedAmount'  => '10',
                    'type'           => 'out_bid'
                ],
            ]
        );

        $this->assertChangedBids(
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
                    'changedAmount'  => '20',
                    'type'           => 'place_bid'
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

    protected function assertChangedBids($oldBids, $newBids, $LoanAmount, $expected)
    {
        $oldAcceptedBids = $this->getAcceptedBids($oldBids, $LoanAmount);
        $newAcceptedBids = $this->getAcceptedBids($newBids, $LoanAmount);

        $method = new ReflectionMethod($this->loanService, 'getChangedBids');
        $method->setAccessible(true);

        $changedBids = $method->invoke($this->loanService, $oldAcceptedBids, $newAcceptedBids);

        $this->assertCount(count($expected), $changedBids);

        foreach ($expected as $id => $haveKeys) {
            $this->assertEquals(Money::create($haveKeys['acceptedAmount']), $changedBids[$id]['acceptedAmount']);
            $this->assertEquals(Money::create($haveKeys['changedAmount']), $changedBids[$id]['changedAmount']);
            $this->assertEquals($haveKeys['type'], $changedBids[$id]['type']);
        }
    }
}