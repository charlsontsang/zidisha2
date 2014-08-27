<?php

namespace Zidisha\Loan\Calculator;


use Carbon\Carbon;
use Zidisha\Admin\Setting;
use Zidisha\Loan\Loan;
use Zidisha\Repayment\RepaymentSchedule;

class RescheduleCalculator {

    /**
     * @var \Zidisha\Repayment\RepaymentSchedule
     */
    private $repaymentSchedule;

    public function __construct(Loan $loan, RepaymentSchedule $repaymentSchedule)
    {
        $this->loan = $loan;
        $this->repaymentSchedule = $repaymentSchedule;
    }

    /**
     * @return Carbon
     */
    public function nextDueDate()
    {
        $nextDueDate = $this->repaymentSchedule->getNextDueDate();
        
        if ($nextDueDate) {
            return Carbon::instance($nextDueDate);
        }

        $now = new \DateTime();
        $lastDueDate = Carbon::instance($this->repaymentSchedule->getLastDueDate());
        $addPeriod = $this->loan->isWeeklyInstallment() ? 'addWeeks' : 'addMonths';

        $i=1;
        while(1) {
            $date = $lastDueDate->copy()->$addPeriod($i);
            if ($date > $now ) {
                return $date;
            }
            $i++;
        }
    }

    public function minInstallmentAmount()
    {   
        $remainingDueAmount = $this->loan->getRemainingDueAmount();
        $remainingPeriod = $this->repaymentSchedule->remainingPeriod();
        
        $amount = $this->loan->getDisbursedAmount()
            ->subtract($this->loan->getForgivenAmount()->multiply($this->loan->getPrincipalRatio()));

        $totalInterestRate = $this->loan->getTotalInterestRate();

        if ($this->loan->isWeeklyInstallment()) {
            $maxAddedPeriod = ceil(Setting::get('loan.maxExtraPeriodRescheduledLoan') / 7);
            $installmentPeriodsInYear = 52;
        } else {
            $maxAddedPeriod = Setting::get('loan.maxExtraPeriodRescheduledLoan');
            $installmentPeriodsInYear = 12;
        }

        $addedInterest = $amount
            ->multiply($totalInterestRate)
            ->multiply($maxAddedPeriod - $remainingPeriod)
            ->divide($installmentPeriodsInYear * 100);
        
        return $remainingDueAmount->add($addedInterest)->divide($maxAddedPeriod)->ceil();
    }

}
