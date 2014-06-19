<?php

namespace Zidisha\Loan;

use SupremeNewMedia\Finance\Core\Currency;
use SupremeNewMedia\Finance\Core\Money;
use Zidisha\Loan\Base\Loan as BaseLoan;

class Loan extends BaseLoan
{

    const OPEN      = 0;
    const FUNDED    = 1;
    const ACTIVE    = 2;
    const REPAID    = 3;
    const DEFAULTED = 5;
    const CANCELED  = 6;
    const EXPIRED   = 7;

    public function getAmount()
    {
        return Money::valueOf(parent::getAmount(), Currency::valueOf($this->getCurrencyCode()));
    }

    public function setAmount(Money $money)
    {
        parent::setAmount($money->getAmount());
    }

    public function getInstallmentAmount()
    {
        return Money::valueOf(parent::getInstallmentAmount(), Currency::valueOf($this->getCurrencyCode()));
    }

    public function setInstallmentAmount(Money $money)
    {
        parent::setInstallmentAmount($money->getAmount());
    }

    public function getUsdAmount()
    {
        return Money::valueOf(parent::getUsdAmount(), Currency::valueOf(Currency::CODE_USD));
    }

    public function setUsdAmount(Money $money)
    {
        parent::setUsdAmount($money->getAmount());
    }
}
