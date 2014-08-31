<?php

namespace Zidisha\Loan;

use Zidisha\Borrower\Borrower;
use Zidisha\Currency\Money;

class ActiveBid
{
    /**
     * @var Money
     */
    protected $repaidAmount;

    /**
     * @var Money
     */
    protected $outstandingAmount;
    
    protected $loanPaymentStatus;

    /**
     * @var Loan
     */
    protected $loan;

    /**
     * @var Borrower
     */
    protected $borrower;

    /**
     * @var Money
     */
    protected $lentAmount;

    public function __construct()
    {
        $this->lentAmount = Money::create(0, 'USD');
    }

    /**
     * @return Money
     */
    public function getRepaidAmount()
    {
        return $this->repaidAmount ?: Money::create(0, 'USD');
    }

    /**
     * @param Money $repaidAmount
     */
    public function setRepaidAmount(Money $repaidAmount)
    {
        $this->repaidAmount = $repaidAmount;
    }

    /**
     * @return Money
     */
    public function getOutstandingAmount()
    {
        return $this->outstandingAmount ?: Money::create(0, 'USD');
    }

    /**
     * @param Money $outstandingAmount
     */
    public function setOutstandingAmount($outstandingAmount)
    {
        $this->outstandingAmount = $outstandingAmount;
    }

    /**
     * @return mixed
     */
    public function getLoanPaymentStatus()
    {
        return $this->loanPaymentStatus;
    }

    /**
     * @return Money
     */
    public function getLentAmount()
    {
        return $this->lentAmount;
    }

    /**
     * @return Loan
     */
    public function getLoan()
    {
        return $this->loan;
    }

    /**
     * @return int
     */
    public function getLoanId()
    {
        return $this->loan->getId();
    }

    /**
     * @return Borrower
     */
    public function getBorrower()
    {
        return $this->borrower;
    }

    /**
     * @param mixed $loanPaymentStatus
     */
    public function setLoanPaymentStatus($loanPaymentStatus)
    {
        $this->loanPaymentStatus = $loanPaymentStatus;
    }

    public function addBid(Bid $bid)
    {
        $this->loan = $bid->getLoan();
        $this->borrower = $bid->getBorrower();
        $this->lentAmount = $this->lentAmount->add($bid->getAcceptedAmount());
    }

    public function getFundedAt()
    {
        return $this->getLoan()->isActive() ? $this->getLoan()->getDisbursedAt() : $this->getLoan()->getAcceptedAt();
    }
}
