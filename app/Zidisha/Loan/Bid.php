<?php

namespace Zidisha\Loan;

use Zidisha\Currency\Money;
use Zidisha\Loan\Base\Bid as BaseBid;

class Bid extends BaseBid
{
    /**
     * @return Money
     */
    public function getBidAmount()
    {
        return Money::create(parent::getBidAmount(), 'USD');
    }

    /**
     * @param Money $money
     * @return $this|Bid
     */
    public function setBidAmount($money)
    {
        return parent::setBidAmount($money->getAmount());
    }

    /**
     * @return Money
     */
    public function getAcceptedAmount()
    {
        return Money::create(parent::getAcceptedAmount(), 'USD');
    }

    /**
     * @param Money $money
     * @return $this|Bid
     */
    public function setAcceptedAmount($money)
    {
        return parent::setAcceptedAmount($money->getAmount());
    }

    public function isFirstBid()
    {
        $lender = $this->getLender();

        if (BidQuery::create()->filterByLender($lender)->findOne()) {
            return false;
        }
        return true;
    }
}
