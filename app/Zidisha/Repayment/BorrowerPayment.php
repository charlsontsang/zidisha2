<?php

namespace Zidisha\Repayment;

use Zidisha\Currency\Money;
use Zidisha\Repayment\Base\BorrowerPayment as BaseBorrowerPayment;

class BorrowerPayment extends BaseBorrowerPayment
{

    public function getAmount()
    {
        return Money::create(parent::getAmount(), 'USD');
    }

    public function setAmount($money)
    {
        return parent::setAmount($money->getAmount());
    }
}

