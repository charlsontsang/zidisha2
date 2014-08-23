<?php
namespace Zidisha\Loan\Calculator;


use Carbon\Carbon;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;
use Zidisha\Repayment\Installment;

class InstallmentCalculator
{
    /**
     * @var \Zidisha\Loan\Loan
     */
    protected $loan;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    /**
     * @return Money
     */
    public function amount()
    {
        return $this->loan->getStatus() >= Loan::ACTIVE ? $this->loan->getDisbursedAmount() : $this->loan->getAmount();
    }
    
    public function period(Money $installmentAmount)
    {
        $maxAnnualInterest = $this->amount()->multiply($this->loan->getMaxInterestRate())->divide(100);

        if ($this->loan->isWeeklyInstallment()) {
            $maxInstallmentInterest = $maxAnnualInterest->divide(52);
        } else {
            $maxInstallmentInterest = $maxAnnualInterest->divide(12);
        }

        $minInstallmentAmount = $installmentAmount->subtract($maxInstallmentInterest);

        // The period includes one grace week/month
        return ceil($this->amount()->ratio($minInstallmentAmount));
    }

    protected function multiplyWithAnnualInterestRateRatio(Money $money)
    {
        if ($this->loan->isWeeklyInstallment()) {
            $periodLength = 7;
            $periodsInYear = 52;
        } else {
            $periodLength = 30;
            $periodsInYear = 12;
        }
        
        $totalTimeLoanInInstallmentPeriods = bcadd(
            $this->loan->getPeriod(),
            bcdiv($this->loan->getExtraDays(), $periodLength, 4),
            4
        );
        
        return $money
            ->multiply($totalTimeLoanInInstallmentPeriods)
            ->divide($periodsInYear);
    }

    public function lenderInterest()
    {
        return $this->multiplyWithAnnualInterestRateRatio($this->amount())
            ->multiply($this->loan->getLenderInterestRate())
            ->divide(100);
    }

    public function serviceFee()
    {
        return $this->multiplyWithAnnualInterestRateRatio($this->amount())
            ->multiply($this->loan->getServiceFeeRate())
            ->divide(100);
    }

    public function totalInterest()
    {
        return $this->serviceFee()->add($this->lenderInterest());
    }

    public function totalAmount()
    {
        return $this->amount()->add($this->totalInterest());
    }

    public function installmentGraceDate()
    {
        $date = Carbon::instance($this->loan->getDisbursedAt());

        return $date->addDays($this->loan->getExtraDays());
    }

    public function nthInstallmentDate($n = 1)
    {
        $date = $this->installmentGraceDate()->copy();

        if ($this->loan->isWeeklyInstallment()) {
            $date->addWeeks($n);
        } else {
            $day = $date->day;
            $hour = $date->hour;
            $minute = $date->minute;
            $second = $date->second;
            $date->firstOfMonth()->addMonths($n);
            
            if ($day >= $date->copy()->lastOfMonth()->day) {
                $date->lastOfMonth();
            } else {
                $date->addDays($day - 1);
            }
            
            $date->setTime($hour, $minute, $second);
        }

        return $date;
    }

    public function generateLoanInstallments()
    {
        $totalAmount = $this->totalAmount();
        $period = $this->loan->getPeriod();
        $installmentAmount = $totalAmount->divide($period)->floor();

        $installments = [];

        for ($count = 0; $count <= $period; $count++) {
            $installment = new Installment();
            $installment
                ->setLoan($this->loan)
                ->setBorrower($this->loan->getBorrower());
            
            if ($count == 0) {
                $installment
                    ->setAmount(Money::create(0, $this->loan->getCurrencyCode()))
                    ->setDueDate($this->installmentGraceDate());
            } elseif ($count == $period) {
                $installment
                    ->setAmount($totalAmount->subtract($installmentAmount->multiply($period-1)))
                    ->setDueDate($this->nthInstallmentDate($count));
            } else {
                $installment
                    ->setAmount($installmentAmount)
                    ->setDueDate($this->nthInstallmentDate($count));
            }
            $installments[] = $installment;
        }

        return $installments;
    }
}
