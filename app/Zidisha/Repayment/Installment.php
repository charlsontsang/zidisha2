<?php

namespace Zidisha\Repayment;

use Zidisha\Currency\Money;
use Zidisha\Repayment\Base\Installment as BaseInstallment;

class Installment extends BaseInstallment
{
    /**
     * @return Money
     */
    public function getAmount()
    {
        return Money::create(parent::getAmount(), 'USD');
    }

    /**
     * @param Money $money
     * @return $this|Installment
     */
    public function setAmount($money)
    {
        return parent::setAmount($money->getAmount());
    }

    /**
     * @return Money
     */
    public function getPaidAmount()
    {
        return Money::create(parent::getPaidAmount(), 'USD');
    }

    /**
     * @param Money $money
     * @return $this|Installment
     */
    public function setPaidAmount($money)
    {
        return parent::setPaidAmount($money->getAmount());
    }

    public function isRepaid()
    {
        return $this->getAmount()->equals($this->getPaidAmount());
    }

    public function getUnpaidAmount()
    {
        return $this->getAmount()->subtract($this->getPaidAmount());
    }

    public function payAmount(Money $amount)
    {
        $this->setPaidAmount($this->getPaidAmount()->add($amount));
        
        return $this;
    }
    
}
