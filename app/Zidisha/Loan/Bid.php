<?php

namespace Zidisha\Loan;

use Zidisha\Currency\Money;
use Zidisha\Loan\Base\Bid as BaseBid;

class Bid extends BaseBid
{
    public function getBidAmount()
    {
        return Money::create(parent::getBidAmount(), 'USD');
    }

    public function setBidAmount($money)
    {
        return parent::setBidAmount($money->getAmount());
    }

    public function getPaidAmount()
    {
        return Money::create(parent::getPaidAmount(), 'USD');
    }

    public function setPaidAmount($money)
    {
        return parent::setPaidAmount($money->getAmount());
    }
}
