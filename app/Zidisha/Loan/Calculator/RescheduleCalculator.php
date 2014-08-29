<?php

namespace Zidisha\Loan\Calculator;


use Carbon\Carbon;
use Zidisha\Admin\Setting;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;
use Zidisha\Repayment\Installment;
use Zidisha\Repayment\RepaymentSchedule;
use Zidisha\Repayment\RepaymentScheduleInstallment;

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

    public function remainingPeriod(Money $installmentAmount)
    {
        $remainingDueAmount = $this->loan->getRemainingDueAmount();
        $remainingPeriod = $this->repaymentSchedule->remainingPeriod();

        $amount = $this->loan->getDisbursedAmount()
            ->subtract($this->loan->getForgivenAmount()->multiply($this->loan->getPrincipalRatio()));

        $totalInterestRate = $this->loan->getTotalInterestRate();

        if ($this->loan->isWeeklyInstallment()) {
            $installmentPeriodsInYear = 52;
        } else {
            $installmentPeriodsInYear = 12;
        }

        $remainingInterest = $amount
            ->multiply($totalInterestRate)
            ->multiply($remainingPeriod)
            ->divide($installmentPeriodsInYear * 100);

        $installmentInterest = $amount
            ->multiply($totalInterestRate)
            ->divide($installmentPeriodsInYear * 100);
        
        $installmentCount = ceil($remainingDueAmount->subtract($remainingInterest)
            ->ratio($installmentAmount->subtract($installmentInterest)));
        
        return $installmentCount - 1;
    }

    public function repaymentScheduleInstallments($installmentAmount, \DateTime $rescheduleDate)
    {
        $zero = Money::create(0, $this->loan->getCurrencyCode());
        $now = new \DateTime();
        
        $repaymentScheduleInstallments = [];
        $deleteRepaymentScheduleInstallments = [];
        $lastDueDate = null;
        
        /** @var RepaymentScheduleInstallment $repaymentScheduleInstallment */
        foreach ($this->repaymentSchedule as $repaymentScheduleInstallment) {
            $installment = $repaymentScheduleInstallment->getInstallment();
            $payments = $repaymentScheduleInstallment->getPayments();

            if ($installment->getPaidAmount()->isPositive()
                && $installment->getPaidAmount()->lessThan($installment->getAmount())
            ) {
                $updatedInstallment = $installment->copyUpdate();
                $updatedInstallment->setAmount($installment->getPaidAmount());
                
                $repaymentScheduleInstallments[] = new RepaymentScheduleInstallment($updatedInstallment, $payments);
            }
            elseif ($installment->getDueDate() < $rescheduleDate) {
                if ($installment->getPaidAmount()->isPositive() || $installment->getAmount()->isZero()) {
                    $repaymentScheduleInstallments[] = $repaymentScheduleInstallment;
                } else {
                    $updatedInstallment = $installment->copyUpdate();
                    $updatedInstallment->setAmount($zero);
                    
                    $repaymentScheduleInstallments[] = new RepaymentScheduleInstallment($updatedInstallment, $payments);
                }
            } else {
                $deleteRepaymentScheduleInstallments[] = $repaymentScheduleInstallment;
                continue;
            }
            $lastDueDate = $installment->getDueDate();
        }
        
        $lastDueDate = Carbon::instance($lastDueDate);
        $addInstallmentPeriods = $this->loan->isWeeklyInstallment() ? 'addWeeks' : 'addMonths';
        $extraPeriod = 0;
        
        if ($lastDueDate < $now) {
            $extraPeriod = 1;
            while(1) {
                $date = $lastDueDate->copy()->$addInstallmentPeriods($extraPeriod);
                if ($date > $now ) {
                    break;
                }
                $extraPeriod += 1;
            }
        }

        $remainingAmount = $this->repaymentSchedule->getRemainingAmountDue();
        $newRemainingPeriod = $this->remainingPeriod($installmentAmount);
        $remainingPeriod = $this->repaymentSchedule->remainingPeriod();
        $amount = $this->loan->getDisbursedAmount()
            ->subtract($this->loan->getForgivenAmount()->multiply($this->loan->getPrincipalRatio()));

        $totalInterestRate = $this->loan->getTotalInterestRate();

        $remainingInterest = $amount
            ->multiply($totalInterestRate)
            ->multiply($newRemainingPeriod - $remainingPeriod + $extraPeriod)
            ->divide($this->loan->getInstallmentPeriodsPerYear() * 100);
        
        $amount = $remainingAmount->add($remainingInterest);
        $i = 1;
        while ($amount->isPositive()) {
            $newInstallmentDate = $lastDueDate->copy()->$addInstallmentPeriods($i);
            if ($newInstallmentDate < $rescheduleDate) {
                $newInstallmentAmount = $zero;
            } else {
                if ($amount->lessThan($installmentAmount)) {
                    $newInstallmentAmount = $amount;
                    $amount = $zero;
                } else {
                    $newInstallmentAmount = $installmentAmount;
                    $amount = $amount->subtract($installmentAmount);
                }
            }
            
            $installment = new Installment();
            $installment
                ->setLoanId($this->loan->getId())
                ->setBorrowerId($this->loan->getBorrowerId())
                ->setAmount($newInstallmentAmount)
                ->setDueDate($newInstallmentDate);
            
            $repaymentScheduleInstallments[] = new RepaymentScheduleInstallment($installment, []);
            $i += 1;
        }

        return [
            'new'     => $repaymentScheduleInstallments,
            'delete'  => $deleteRepaymentScheduleInstallments,
        ];
    }

}
