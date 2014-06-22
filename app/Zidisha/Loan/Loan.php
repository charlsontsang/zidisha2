<?php

namespace Zidisha\Loan;

use Zidisha\Currency\Money;
use Zidisha\Loan\Base\Loan as BaseLoan;

class Loan extends BaseLoan
{

    const OPEN      = 0;
    const FUNDED    = 1;
    const ACTIVE    = 2;
    const REPAID    = 3;
    const NO_LOAN   = 4;
    const DEFAULTED = 5;
    const CANCELED  = 6;
    const EXPIRED   = 7;

    public static function createFromData($data)
    {
        $currency = $data['currencyCode'];

        $loan = new Loan();
        $loan->setSummary($data['summary']);
        $loan->setDescription($data['description']);

        $loan->setCurrencyCode($data['currencyCode']);
        $loan->setNativeAmount(Money::create($data['nativeAmount'], $currency));
        $loan->setInstallmentAmount(Money::create($data['installmentAmount'], $currency));

        $loan->setAmount(Money::create($data['amount'], 'USD'));
        $loan->setRegistrationFeeRate('5');

        $loan->setInstallmentDay($data['installmentDay']);
        $loan->setApplicationDate(new \DateTime());

        return $loan;
    }

    public function getNativeAmount()
    {
        return Money::create(parent::getnativeAmount(), $this->getCurrencyCode());
    }

    public function setNativeAmount($money)
    {
        return parent::setNativeamount($money->getAmount());
    }

    public function getInstallmentAmount()
    {
        return Money::create(parent::getInstallmentAmount(), $this->getCurrencyCode());
    }

    public function setInstallmentAmount($money)
    {
        return parent::setInstallmentAmount($money->getAmount());
    }

    public function getAmount()
    {
        return Money::create(parent::getmount(), 'USD');
    }

    public function setAmount($money)
    {
        return parent::setAmount($money->getAmount());
    }
}
