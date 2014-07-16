<?php
namespace Zidisha\Loan\Calculator;


use Carbon\Carbon;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;

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

    public function calculateInstallmentCount(Money $installmentAmount)
    {
        $maxYearlyInterest = $this->loan->getAmount()->multiply($this->loan->getInterestRate() / 100);

        if ($this->loan->isWeeklyInstallment()) {
            $maxInstallmentInterest = $maxYearlyInterest->divide(52);
        } else {
            $maxInstallmentInterest = $maxYearlyInterest->divide(12);
        }

        $minInstallmentAmount = $installmentAmount->subtract($maxInstallmentInterest);

        $installmentCount = ceil($this->loan->getAmount()->getAmount() / $minInstallmentAmount->getAmount());

        return $installmentCount;
    }

    public function yearlyInterestRateRatio()
    {
        if ($this->loan->isWeeklyInstallment()) {
            $totalTimeLoanInWeeks = $this->loan->getInstallmentCount() + round($this->loan->getExtraDays() / 7, 4);
            return $totalTimeLoanInWeeks / 52;
        }

        $totalTimeLoanInMonths = $this->loan->getInstallmentCount() + round($this->loan->getExtraDays() / 30, 4);
        return $totalTimeLoanInMonths / 12;
    }

    public function lenderInterest()
    {
        return $this->loan->getAmount()
            ->multiply($this->yearlyInterestRateRatio() * $this->loan->getFinalInterestRate() / 100);
    }

    public function serviceFee()
    {
        return $this->loan->getAmount()
            ->multiply($this->yearlyInterestRateRatio() * $this->loan->getServiceFeeRate() / 100);
    }

    public function totalInterest()
    {
        return $this->serviceFee()->add($this->lenderInterest());
    }

    public function totalAmount()
    {
        return $this->loan->getAmount()->add($this->totalInterest());
    }

    public function installmentAmount()
    {
        return $this->totalAmount()->divide($this->loan->getInstallmentCount());
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
            if ($date->day == 31) {
                $date->firstOfMonth()->addMonths($n)->lastOfMonth();
            } else {
                $date->addMonths($n);
            }
        }

        return $date;
    }
}
