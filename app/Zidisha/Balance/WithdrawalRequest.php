<?php

namespace Zidisha\Balance;

use Zidisha\Balance\Base\WithdrawalRequest as BaseWithdrawalRequest;
use Zidisha\Currency\Money;

class WithdrawalRequest extends BaseWithdrawalRequest
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
