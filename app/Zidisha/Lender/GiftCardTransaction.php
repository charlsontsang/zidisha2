<?php

namespace Zidisha\Lender;

use Zidisha\Currency\Money;
use Zidisha\Lender\Base\GiftCardTransaction as BaseGiftCardTransaction;

class GiftCardTransaction extends BaseGiftCardTransaction
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
