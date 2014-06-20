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

    public static function createFromData($data)
    {
        $currency = Currency::valueOf($data['currencyCode']);

        $loan = new Loan();
        $loan->setSummary($data['summary']);
        $loan->setDescription($data['description']);

        $loan->setCurrencyCode($data['currencyCode']);
        $loan->setAmount(Money::valueOf($data['amount'], $currency));
        $loan->setInstallmentAmount(Money::valueOf($data['installmentAmount'], $currency));

        $loan->setUsdAmount(Money::valueOf($data['usdAmount'], Currency::valueOf('USD')));
        $loan->setRegistrationFeeRate('5');

        $loan->setInstallmentDay($data['installmentDay']);
        $loan->setApplicationDate(new \DateTime());

        return $loan;
    }

    public function getAmount()
    {
        return Money::valueOf(parent::getAmount(), Currency::valueOf($this->getCurrencyCode()));
    }

    public function setAmount($money)
    {
        return parent::setAmount($money->getAmount());
    }

    public function getInstallmentAmount()
    {
        return Money::valueOf(parent::getInstallmentAmount(), Currency::valueOf($this->getCurrencyCode()));
    }

    public function setInstallmentAmount($money)
    {
        return parent::setInstallmentAmount($money->getAmount());
    }

    public function getUsdAmount()
    {
        return Money::valueOf(parent::getUsdAmount(), Currency::valueOf(Currency::CODE_USD));
    }

    public function setUsdAmount($money)
    {
        return parent::setUsdAmount($money->getAmount());
    }
}
