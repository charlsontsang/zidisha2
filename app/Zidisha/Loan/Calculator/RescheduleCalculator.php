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

    public function remainingPeriod(\Datetime $date, Money $installmentAmount)
    {
        $remainingDueAmount = $this->repaymentSchedule->getRemainingDueAmount();
        $remainingPeriod = $this->repaymentSchedule->remainingPeriod($date);

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

        $installmentInterest = $installmentAmount
            ->multiply($totalInterestRate)
            ->divide($installmentPeriodsInYear * 100);
        
        $installmentCount = ceil($remainingDueAmount->subtract($remainingInterest)
            ->ratio($installmentAmount->subtract($installmentInterest)));
        
        return $installmentCount - 1;
    }

    public function repaymentScheduleInstallments($installmentAmount, \DateTime $rescheduleDate)
    {
        $zero = Money::create(0, $this->loan->getCurrencyCode());
        
        $repaymentScheduleInstallments = [];
        $deleteRepaymentScheduleInstallments = [];
        $rescheduleDate = Carbon::instance($rescheduleDate);
        
        /** @var RepaymentScheduleInstallment $repaymentScheduleInstallment */
        foreach ($this->repaymentSchedule as $repaymentScheduleInstallment) {
            $installment = $repaymentScheduleInstallment->getInstallment();
            $payments = $repaymentScheduleInstallment->getPayments();

            if ($installment->getPaidAmount()->isPositive()) {
                if ($installment->getPaidAmount()->lessThan($installment->getAmount())) {
                    $updatedInstallment = $installment->copyUpdate();
                    $updatedInstallment->setAmount($installment->getPaidAmount());

                    $repaymentScheduleInstallments[] = new RepaymentScheduleInstallment($updatedInstallment, $payments);
                } else {
                    $repaymentScheduleInstallments[] = $repaymentScheduleInstallment;
                }
            }
            elseif ($installment->getDueDate() < $rescheduleDate) {
                if ($installment->getAmount()->isZero()) {
                    $repaymentScheduleInstallments[] = $repaymentScheduleInstallment;
                } else {
                    $updatedInstallment = $installment->copyUpdate();
                    $updatedInstallment->setAmount($zero);
                    
                    $repaymentScheduleInstallments[] = new RepaymentScheduleInstallment($updatedInstallment, $payments);
                }
            } else {
                $deleteRepaymentScheduleInstallments[] = $repaymentScheduleInstallment;
            }
        }    
           
        $addInstallmentPeriods = $this->loan->isWeeklyInstallment() ? 'addWeeks' : 'addMonths';
        $extraPeriod = 0;

        // we only allow rescheduling after the borrower made at least one payment and after the first installment (?TODO),
        // hence $repaymentScheduleInstallments is never empty
        /** @var RepaymentScheduleInstallment $lastRepaymentScheduleInstallment */
        $lastRepaymentScheduleInstallment = $repaymentScheduleInstallments[count($repaymentScheduleInstallments)-1];
        $lastDueDate = Carbon::instance($lastRepaymentScheduleInstallment->getInstallment()->getDueDate());
        
        if ($deleteRepaymentScheduleInstallments) {
            /** @var RepaymentScheduleInstallment $nextRepaymentScheduleInstallment */
            $nextRepaymentScheduleInstallment = $deleteRepaymentScheduleInstallments[0];
            $nextDueDate = Carbon::instance($nextRepaymentScheduleInstallment->getInstallment()->getDueDate());
        } else {
            $extraPeriod = 1;
            $nextDueDate = $lastDueDate->copy()->$addInstallmentPeriods(1);
            
            while ($nextDueDate < $rescheduleDate) {
                $extraPeriod += 1;
                $nextDueDate = $lastDueDate->copy()->$addInstallmentPeriods($extraPeriod);
            }
        }
        
        $remainingAmount = $this->repaymentSchedule->getRemainingAmountDue();
        $newRemainingPeriod = $this->remainingPeriod($nextDueDate, $installmentAmount);
        $remainingPeriod = $this->repaymentSchedule->remainingPeriod($nextDueDate);
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
            if ($newInstallmentDate < $nextDueDate) {
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
