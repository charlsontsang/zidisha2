<?php

namespace Zidisha\Lender;

use Zidisha\Currency\Money;
use Zidisha\Lender\Base\AutoLendingSetting as BaseAutoLendingSetting;

class AutoLendingSetting extends BaseAutoLendingSetting
{
    const HIGH_FEEDBCK_RATING = 1;
    const EXPIRE_SOON = 2;
    const HIGH_OFFER_INTEREST = 3;
    const HIGH_NO_COMMENTS = 4;
    const LOAN_RANDOM = 5;
    const AUTO_LEND_AS_PREV_LOAN  = 6;

    public function setLenderCredit(Money $money)
    {
        return parent::setLenderCredit($money->getAmount());        
    }

    public function getLenderCredit()
    {
        return Money::create(parent::getLenderCredit(), 'USD');
    }
}
