<?php

namespace Zidisha\Lender;

use Zidisha\Currency\Money;
use Zidisha\Lender\Base\Card as BaseCard;

class Card extends BaseCard
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
}
