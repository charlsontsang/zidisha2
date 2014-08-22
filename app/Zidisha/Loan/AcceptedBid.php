<?php

namespace Zidisha\Loan;


use Zidisha\Currency\Money;

class AcceptedBid {

    /**
     * @var Bid
     */
    private $bid;

    /**
     * @var Money
     */
    private $acceptedAmount;

    public function __construct(Bid $bid, Money $acceptedAmount)
    {
        $this->bid = $bid;
        $this->acceptedAmount = $acceptedAmount;
    }

    /**
     * @return \Zidisha\Loan\Bid
     */
    public function getBid()
    {
        return $this->bid;
    }

    /**
     * @return \Zidisha\Currency\Money
     */
    public function getAcceptedAmount()
    {
        return $this->acceptedAmount;
    }
}
