<?php

namespace Zidisha\Loan;

use SupremeNewMedia\Finance\Core\Currency;
use SupremeNewMedia\Finance\Core\Money;
use Zidisha\Loan\Base\Bid as BaseBid;

class Bid extends BaseBid
{
    public function getBidAmount()
    {
        return Money::valueOf(parent::getBidAmount(), Currency::valueOf('USD'));
    }

    public function setBidAmount($money)
    {
        return parent::setBidAmount($money->getAmount());
    }

    public function getPaidAmount()
    {
        return Money::valueOf(parent::getPaidAmount(), Currency::valueOf('USD'));
    }

    public function setPaidAmount($money)
    {
        return parent::setPaidAmount($money->getAmount());
    }
}
