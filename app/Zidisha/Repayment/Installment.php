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
        return Money::create(parent::getAmount(), $this->getLoan()->getCurrencyCode());
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

    /**
     * @see Installment::copy
     * @return Installment
     */
    public function copyUpdate()
    {
        $copyObj = new Installment();
        $copyObj->setId($this->getId());
        $copyObj->setBorrowerId($this->getBorrowerId());
        $copyObj->setLoanId($this->getLoanId());
        $copyObj->setDueDate($this->getDueDate());
        $copyObj->setAmount($this->getAmount());
        $copyObj->setPaidDate($this->getPaidDate());
        // Otherwise it will set PaidAmount to O instead of null
        if ($this->getPaidAmount()->isPositive()) {
            $copyObj->setPaidAmount($this->getPaidAmount());
        }
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());
        $copyObj->setNew(false);
        
        return $copyObj;
    }

    public function isGracePeriod()
    {
        // TODO fix this for rescheduled loans
        return $this->getAmount()->isZero();
    }

}
