<?php

namespace Zidisha\Loan\Calculator;


use Zidisha\Currency\Converter;
use Zidisha\Currency\ExchangeRate;
use Zidisha\Currency\Money;
use Zidisha\Loan\Bid;
use Zidisha\Loan\Loan;
use Zidisha\Repayment\LoanRepayment;

class RepaymentCalculator extends InstallmentCalculator
{

    /**
     * @var \Zidisha\Loan\Loan
     */
    protected $loan;

    /**
     * @var Money
     */
    protected $paidServiceFee;

    /**
     * @var Money
     */
    protected $paidAmount;

    /**
     * @var Money
     */
    protected $totalAmount;

    /**
     * @var Money
     */
    protected $repaymentAmount;

    /**
     * @var Money
     */
    protected $forgivenAmount;

    
    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    public function setRepaymentAmount(Money $repaymentAmount)
    {
        $this->repaymentAmount = $repaymentAmount;

        return $this;
    }

    public function setTotalAmount(Money $totalAmount)
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function setPaidAmount(Money $totalPaidAmount)
    {
        $this->paidAmount = $totalPaidAmount;

        return $this;
    }

    public function setPaidServiceFee(Money $totalPaidServiceFeeAmount)
    {
        $this->paidServiceFee = $totalPaidServiceFeeAmount;

        return $this;
    }

    public function setForgivenAmount(Money $forgivenAmount)
    {
        $this->forgivenAmount = $forgivenAmount;

        return $this;
    }

    public function unpaidAmount()
    {
        return $this->totalAmount->subtract($this->paidAmount);
    }

    public function refundAmount(Money $threshold)
    {
        // todo include forgiven?
        $unpaidAmount = $this->unpaidAmount();
        $thresholdAmount = $unpaidAmount->add($threshold);

        if ($this->repaymentAmount->greaterThan($thresholdAmount)) {
            return $this->repaymentAmount->subtract($unpaidAmount->ceil());
        }
        
        return Money::create(0);
    }

    public function openAmount()
    {
        return $this
            ->totalAmount()
            ->subtract($this->paidAmount)
            ->subtract($this->forgivenAmount);
    }

    public function repaymentServiceFee()
    {
        $unpaidServiceFee = $this->serviceFee()->subtract($this->paidServiceFee);
        $ratio = $this->repaymentAmount->ratio($this->openAmount());

        return $unpaidServiceFee->multiply($ratio);
    }
    
    public function repaymentAmountForLenders()
    {
        return $this->repaymentAmount->subtract($this->repaymentServiceFee());
    }

    /**
     * @param $bids
     * @return array Bid
     */
    public function loanRepayments(ExchangeRate $exchangeRate, $bids)
    {
        $loanRepayments = [];
        $totalAmount = Money::create(0);
        
        $repaymentAmountForLenders = Converter::toUsd($this->repaymentAmountForLenders(), $exchangeRate);

        /* @var $bid Bid */
        foreach ($bids as $bid) {
            $totalAmount = $totalAmount->add($bid->getAcceptedAmount());
        }

        /* @var $bid Bid */
        foreach ($bids as $bid) {
            $lender = $bid->getLender();
            if (isset($loanRepayments[$lender->getId()])) {
                $loanRepayment = $loanRepayments[$lender->getId()];
            } else {
                $loanRepayment = new LoanRepayment($lender);
            }

            $share = $bid->getAcceptedAmount()->ratio($totalAmount);
            $repaidAmount = $repaymentAmountForLenders->multiply($share);

            $loanRepayment->addRepaidAmount($repaidAmount, $bid->getLenderInviteCredit());
        }
        
        return $loanRepayments;
    }

    public function isRepaid()
    {
        return $this->unpaidAmount()
            ->subtract($this->repaymentAmount)
            ->lessThan(Money::create(1, $this->repaymentAmount->getCurrency()));
    }
}
