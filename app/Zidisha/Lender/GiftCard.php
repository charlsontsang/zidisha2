<?php

namespace Zidisha\Lender;

use Zidisha\Lender\Base\GiftCard as BaseGiftCard;
use Zidisha\Currency\Money;

class GiftCard extends BaseGiftCard
{

    public function getCardAmount()
    {
        return Money::create(parent::getCardAmount(), 'USD');
    }

    public function setCardAmount($money)
    {
        return parent::setCardAmount($money->getAmount());
    }

    public function getStringClaimed()
    {
        $status = $this->getClaimed();
        if($status == 0){
            return "Not Yet Redeemed";
        }
        return "Redeemed";
    }

    public function setOrderType($type)
    {
        if($type = 0){
            return parent::setOrderType('Self-Print');
        }
        return parent::setOrderType('Email');
    }
}
