<?php

namespace Zidisha\Repayment;

use Zidisha\Currency\Money;
use Zidisha\Repayment\Base\BorrowerRefund as BaseBorrowerRefund;

class BorrowerRefund extends BaseBorrowerRefund
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
