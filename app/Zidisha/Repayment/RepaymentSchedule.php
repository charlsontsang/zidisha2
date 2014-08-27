<?php

namespace Zidisha\Repayment;


use Carbon\Carbon;
use Traversable;
use Zidisha\Currency\Converter;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;

class RepaymentSchedule implements \IteratorAggregate
{

    protected $totalAmountDue;
    protected $totalAmountPaid;
    private $installments = [];
    private $paidInstallmentCount;
    private $missedInstallmentCount;
    private $paidOnTimeInstallmentCount;
    private $todayInstallmentCount;
    private $loanPaymentStatus;
    private $overDueInstallmentCount;
    private $loan;

    public function __construct(Loan $loan, $installments)
    {
        $this->installments = $installments;
        $this->loan = $loan;
        $this->calculateInstallmentsCounts();
    }

    public function getInstallments()
    {
        return $this->installments;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->installments);
    }

    protected function calculateInstallmentsCounts()
    {
        $zero = Money::create(0, $this->loan->getCurrency());
        $today = new Carbon();
        $repaymentThreshold = \Config::get('constants.repaymentThreshold');
        $repaymentThresholdAmount = Money::create(\Config::get('constants.repaymentAmountThreshold'), 'USD');
        $isActiveLoan = $this->loan->getStatus() == Loan::ACTIVE;
        $paidInstallmentCount = 0;
        $overDueInstallmentCount = 0;
        $missedInstallmentCount = 0;
        $paidOnTimeInstallmentCount = 0;
        $todayInstallmentCount = 0;
        $totalAmountDue = $zero;
        $totalAmountPaid = $zero;
        $paymentStatus = 'on-time';
        $dueDateThreshold = $isActiveLoan ? $repaymentThreshold : 0;
        $endedAt = $this->loan->getEndedAt();
        $maximumDueDate = $endedAt ? $endedAt : $today->subDays($dueDateThreshold);

        /** @var RepaymentScheduleInstallment $repaymentScheduleInstallment */
        foreach ($this as $repaymentScheduleInstallment) {
            $thresholdAmount = $zero;
            $totalEarlyPaidAmount = $zero;
            $missedInstallmentAmount = $zero;
            $totalPaidInstallmentAmount = $zero;
            $dueInstallmentAmount = $repaymentScheduleInstallment->getInstallment()->getAmount();
            $dueInstallmentDate = Carbon::instance($repaymentScheduleInstallment->getInstallment()->getDueDate());
            $isTodayInstallment = $dueInstallmentDate <= $maximumDueDate;
            $totalAmountDue = $totalAmountDue->add($dueInstallmentAmount);

            /** @var RepaymentScheduleInstallmentPayment $repaymentScheduleInstallmentPayment */
            foreach ($repaymentScheduleInstallment->getPayments() as $repaymentScheduleInstallmentPayment) {
                $installmentPaymentPaidDate = Carbon::instance($repaymentScheduleInstallmentPayment->getPayment()->getPaidDate());
                $installmentPaymentPaidAmount = $repaymentScheduleInstallmentPayment->getAmount();
                $exchangeRate = $repaymentScheduleInstallmentPayment->getPayment()->getExchangeRate();
                $thresholdAmount = Converter::fromUSD($repaymentThresholdAmount, $this->loan->getCurrency(), $exchangeRate);
                if ($dueInstallmentAmount->lessThan($thresholdAmount) && $dueInstallmentAmount->isPositive()) {
                    $thresholdAmount = $dueInstallmentAmount;
                }
                if ($installmentPaymentPaidDate && (($dueInstallmentDate->diffInDays($installmentPaymentPaidDate) > $repaymentThreshold))) {
                    $totalEarlyPaidAmount = $totalEarlyPaidAmount->add($installmentPaymentPaidAmount);
                }
                if (empty($installmentPaymentPaidDate)) {
                    $missedInstallmentAmount = $missedInstallmentAmount->add($dueInstallmentAmount);
                } elseif ($dueInstallmentDate->diffInDays($installmentPaymentPaidDate, false) > $repaymentThreshold) {
                    $missedInstallmentAmount = $missedInstallmentAmount->add($installmentPaymentPaidAmount);
                }
                $totalPaidInstallmentAmount = $totalPaidInstallmentAmount->add($installmentPaymentPaidAmount);
                $totalAmountPaid = $totalAmountPaid->add($installmentPaymentPaidAmount);
            }
            $isInstallmentPaid = $totalPaidInstallmentAmount && ($dueInstallmentAmount->subtract(
                    $totalPaidInstallmentAmount
                )->lessThanOrEqual($thresholdAmount));
            $isInstallmentPaidOnTime = $isInstallmentPaid && ($missedInstallmentAmount->lessThanOrEqual($thresholdAmount));
            $isInstallmentPaidEarly = $thresholdAmount && ($thresholdAmount->lessThan($totalEarlyPaidAmount));

            if ($isInstallmentPaid) {
                $paidInstallmentCount++;
            }
            if ($isInstallmentPaidOnTime && !$endedAt && $isActiveLoan
                && $today->diffInDays($dueInstallmentDate, false) >= $repaymentThreshold
            ) {
                $isTodayInstallment = true;
            }

            if ($isTodayInstallment) {
                $todayInstallmentCount += 1;
                if ($isInstallmentPaidEarly) {
                    $paymentStatus = 'early';
                } else {
                    $paymentStatus = $isInstallmentPaidOnTime ? 'on-time' : 'late';
                }
                if ($isInstallmentPaidOnTime) {
                    $paidOnTimeInstallmentCount += 1;
                } else {
                    $missedInstallmentCount += 1;
                }
            } elseif ($isTodayInstallment) {
                $paymentStatus = 'late';
            } elseif ($isInstallmentPaidEarly) {
                $paymentStatus = 'early';
            }
            $currentTime = Carbon::now();

            if(!$repaymentScheduleInstallment->getPayments() && $dueInstallmentDate->isPast() &&$dueInstallmentAmount->isPositive()) {
                $overDueInstallmentCount++;
            }
        }

        $this->loanPaymentStatus = $paymentStatus;
        $this->todayInstallmentCount = $todayInstallmentCount;
        $this->paidInstallmentCount = $paidInstallmentCount;
        $this->missedInstallmentCount = $missedInstallmentCount;
        $this->paidOnTimeInstallmentCount = $paidOnTimeInstallmentCount;
        $this->totalAmountDue = $totalAmountDue;
        $this->totalAmountPaid = $totalAmountPaid;
        $this->overDueInstallmentCount = $overDueInstallmentCount;
    }

    public function getPaidInstallmentCount()
    {
        return $this->paidInstallmentCount;
    }

    public function getMissedInstallmentCount()
    {
        return $this->missedInstallmentCount;
    }

    public function getPaidOnTimeInstallmentCount()
    {
        return $this->paidOnTimeInstallmentCount;
    }

    public function getTodayInstallmentCount()
    {
        return $this->todayInstallmentCount;
    }

    public function getLoanPaymentStatus()
    {
        return $this->loanPaymentStatus;
    }

    /**
     * @return Money
     */
    public function getTotalAmountDue()
    {
        return $this->totalAmountDue;
    }

    /**
     * @return Money
     */
    public function getTotalAmountPaid()
    {
        return $this->totalAmountPaid;
    }

    /**
     * @return Money
     */
    public function getRemainingDueAmount()
    {
        return $this->getTotalAmountDue()->subtract($this->getTotalAmountPaid());
    }

    public function getRemainingAmountDue()
    {
        return $this->getTotalAmountDue()
            ->subtract($this->getTotalAmountPaid())
            ->max(Money::create(0, $this->loan->getCurrencyCode()));
    }

    public function getTotalInterest()
    {
        return $this->getTotalAmountDue()->subtract($this->loan->getDisbursedAmount());
    }

    public function getLastDueDate()
    {
        /** @var RepaymentScheduleInstallment $repaymentScheduleInstallment */
        $repaymentScheduleInstallment = $this->installments[count($this->installments) - 1];
        
        return $repaymentScheduleInstallment->getInstallment()->getDueDate();
    }

    /**
     * @return \DateTime
     */
    public function getNextDueDate()
    {
        $now = new \DateTime();
        
        /** @var RepaymentScheduleInstallment $repaymentScheduleInstallment */
        foreach ($this as $repaymentScheduleInstallment) {
            $installment = $repaymentScheduleInstallment->getInstallment();
            if ($installment->getDueDate() > $now && 
                $installment->getPaidAmount()->isZero() &&
                $installment->getAmount()->isPositive()
            ) {
               return $installment->getDueDate(); 
            }
        }
        
        return null;
    }

    public function remainingPeriod()
    {
        $now = new \DateTime();
        $i = 0;

        /** @var RepaymentScheduleInstallment $repaymentScheduleInstallment */
        foreach ($this as $repaymentScheduleInstallment) {
            $installment = $repaymentScheduleInstallment->getInstallment();
            if ($installment->getDueDate() > $now) {
                $i += 1;
            }
        }

        return $i;
    }

    public function getPeriod()
    {
        return count($this->installments);
    }

    public function getOverDueInstallmentCount()
    {
        return $this->overDueInstallmentCount;
    }
}
