<?php

namespace Zidisha\Loan\Calculator;


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

    public function installmentServiceFee()
    {
        $unpaidServiceFee = $this->serviceFee()->subtract($this->paidServiceFee);
        // todo AmountGot
        // todo totalamount confusion
        $openAmount = $this->totalAmount()
            ->subtract($this->paidAmount)
            ->subtract($this->forgivenAmount);
        // Todo money divide (times, ...)
        $ratio = $this->repaymentAmount->divide($openAmount);
        
        return $unpaidServiceFee->multiply($ratio);

    }
    
    public function repaymentAmountForLenders()
    {
        // TODO use disbursed amount in lenderInterest
        $totalLendersAmount = $this->loan->getNativeDisbursedAmount()->add($this->lenderInterest());
        $ratio = $this->loan->getNativeDisbursedAmount()->divide($totalLendersAmount);
        
        $repaymentAmountForLenders = $this->repaymentAmount->subtract($this->installmentServiceFee());

        return $repaymentAmountForLenders->multiply($ratio);
    }

    /**
     * @param $bids
     * @return array Bid
     */
    public function loanRepayments($bids)
    {
        $installmentPayments = [];
        $totalAmount = Money::create(0);

        /* @var $bid Bid */
        foreach ($bids as $bid) {
            $lender = $bid->getLender();
            if (isset($installmentPayments[$lender->getId()])) {
                $installmentPayment = $installmentPayments[$lender->getId()];
            } else {
                $installmentPayment = new LoanRepayment($lender);
            }
            
            $installmentPayment->addBid($bid);

            // TODO why is this not equal to $loan->getAmount()
            $totalAmount = $totalAmount->add($bid->getAcceptedAmount());
        }

        /* @var $installmentPayment LoanRepayment */
        foreach ($installmentPayments as $installmentPayment) {
            $share = $installmentPayment->getTotalAcceptedAmount()->divide($totalAmount);
            $installmentPayment->setShare($share);
        }
        
        return $installmentPayments;
    }
}
