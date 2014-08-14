<?php

namespace Zidisha\Balance;

use Zidisha\Balance\Base\InviteTransaction as BaseInviteTransaction;
use Zidisha\Currency\Money;

class InviteTransaction extends BaseInviteTransaction
{
    public function getAmount()
    {
        return Money::create(parent::getAmount(), 'USD');
    }

    /**
     * @param Money $money
     * @return $this|InviteTransaction
     */
    public function setAmount($money)
    {
        return parent::setAmount($money->getAmount());
    }
}
