<?php

namespace Zidisha\Repayment;

use Zidisha\Currency\Money;
use Zidisha\Repayment\Base\Installment as BaseInstallment;

class Installment extends BaseInstallment
{
    /**
     * @return Money
     */
    public function getNativeAmount()
    {
        return Money::create(parent::getNativeAmount(), 'USD');
    }

    /**
     * @param Money $money
     * @return $this|Installment
     */
    public function setNativeAmount($money)
    {
        return parent::setNativeAmount($money->getAmount());
    }

    /**
     * @return Money
     */
    public function getNativePaidAmount()
    {
        return Money::create(parent::getNativePaidAmount(), 'USD');
    }

    /**
     * @param Money $money
     * @return $this|Installment
     */
    public function setNativePaidAmount($money)
    {
        return parent::setNativePaidAmount($money->getAmount());
    }

    public function isRepaid()
    {
        return $this->getNativeAmount()->equals($this->getNativePaidAmount());
    }

    public function getUnpaidAmount()
    {
        return $this->getNativeAmount()->subtract($this->getNativePaidAmount());
    }

    public function payAmount(Money $amount)
    {
        $this->setNativePaidAmount($this->getNativePaidAmount()->add($amount));
        
        return $this;
    }
    
}
