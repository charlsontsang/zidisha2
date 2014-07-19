<?php

namespace Zidisha\Repayment;

use Zidisha\Currency\Money;
use Zidisha\Repayment\Base\InstallmentPayment as BaseInstallmentPayment;

class InstallmentPayment extends BaseInstallmentPayment
{
    /**
     * @return Money
     */
    public function getPaidAmount()
    {
        return Money::create(parent::getPaidAmount(), $this->getLoan()->getCurrencyCode());
    }

    /**
     * @param Money $money
     * @return $this|Installment
     */
    public function setPaidAmount($money)
    {
        return parent::setPaidAmount($money->getAmount());
    }
}
