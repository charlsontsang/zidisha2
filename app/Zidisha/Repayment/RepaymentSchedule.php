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

    private $installments = [];
    private $paidInstallmentCount, $missedInstallmentCount, $paidOnTimeInstallmentCount, $todayInstallmentCount;
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
        $today = new Carbon();
        $repaymentThreshold = \Config::get('constants.repaymentThreshold');
        $repaymentThresholdAmount = Money::create(\Config::get('constants.repaymentAmountThreshold'), 'USD');
        $isActiveLoan = $this->loan->getStatus() == Loan::ACTIVE;
        $paidInstallmentCount = 0;
        $missedInstallmentCount = 0;
        $paidOnTimeInstallmentCount = 0;
        $todayInstallmentCount = 0;
        $dueDateThreshold = $isActiveLoan ? $repaymentThreshold : 0;
        $endedAt = $this->loan->getEndedAt();
        $maximumDueDate = $endedAt ? $endedAt : $today->subDays($dueDateThreshold);
        $zero = Money::create(0, $this->loan->getCurrency());

            /** @var RepaymentScheduleInstallment $repaymentScheduleInstallment */
            foreach ($this as $repaymentScheduleInstallment) {
                $thresholdAmount = $zero;
                $missedInstallmentAmount = $zero;
                $totalPaidInstallmentAmount = $zero;
                $dueInstallmentAmount = $repaymentScheduleInstallment->getInstallment()->getAmount();
                $dueInstallmentDate = Carbon::instance($repaymentScheduleInstallment->getInstallment()->getDueDate());
                $isTodayInstallment = $dueInstallmentDate <= $maximumDueDate;

                /** @var RepaymentScheduleInstallmentPayment $repaymentScheduleInstallmentPayment */
                foreach ($repaymentScheduleInstallment->getPayments() as $repaymentScheduleInstallmentPayment) {
                    $installmentPaymentPaidDate = Carbon::instance($repaymentScheduleInstallmentPayment->getPayment()->getPaidDate());
                    $installmentPaymentPaidAmount = $repaymentScheduleInstallmentPayment->getAmount();
                    $exchangeRate = $repaymentScheduleInstallmentPayment->getPayment()->getExchangeRate();
                    $thresholdAmount = Converter::fromUSD($repaymentThresholdAmount, $this->loan->getCurrency(), $exchangeRate);
                    if ($dueInstallmentAmount->lessThan($thresholdAmount) && $dueInstallmentAmount->isPositive()) {
                        $thresholdAmount = $dueInstallmentAmount;
                    }
                    if (empty($installmentPaymentPaidDate)) {
                        $missedInstallmentAmount = $missedInstallmentAmount->add($dueInstallmentAmount);
                    } elseif ($dueInstallmentDate->diffInDays($installmentPaymentPaidDate, false) > $repaymentThreshold) {
                        $missedInstallmentAmount = $missedInstallmentAmount->add($installmentPaymentPaidAmount);
                    }
                    $totalPaidInstallmentAmount = $totalPaidInstallmentAmount->add($installmentPaymentPaidAmount);
                }
                $isInstallmentPaid = $totalPaidInstallmentAmount && ($dueInstallmentAmount->subtract(
                        $totalPaidInstallmentAmount
                    )->lessThanOrEqual($thresholdAmount));
                $isInstallmentPaidOnTime = $isInstallmentPaid && ($missedInstallmentAmount->lessThanOrEqual($thresholdAmount));

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
                    if ($isInstallmentPaidOnTime) {
                        $paidOnTimeInstallmentCount += 1;
                    } else {
                        $missedInstallmentCount += 1;
                    }
                }
            }

        $this->todayInstallmentCount = $todayInstallmentCount;
        $this->paidInstallmentCount = $paidInstallmentCount;
        $this->missedInstallmentCount = $missedInstallmentCount;
        $this->paidOnTimeInstallmentCount = $paidOnTimeInstallmentCount;
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

}