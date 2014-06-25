<?php

namespace Zidisha\Loan;

use Carbon\Carbon;
use Zidisha\Currency\Money;
use Zidisha\Loan\Base\Loan as BaseLoan;

class Loan extends BaseLoan
{

    const OPEN = 0;
    const FUNDED = 1;
    const ACTIVE = 2;
    const REPAID = 3;
    const NO_LOAN = 4;
    const DEFAULTED = 5;
    const CANCELED = 6;
    const EXPIRED = 7;
    
    const WEEKLY_INSTALLMENT  = 'week';
    const MONTHLY_INSTALLMENT = 'month';

    public static function createFromData($data)
    {
        $currency = $data['currencyCode'];

        $loan = new Loan();
        $loan->setSummary($data['summary']);
        $loan->setDescription($data['description']);

        $loan->setCurrencyCode($data['currencyCode']);
        $loan->setNativeAmount(Money::create($data['nativeAmount'], $currency));

        $loan->setAmount(Money::create($data['amount'], 'USD'));
        $loan->setRegistrationFeeRate('5');
        $loan->setInstallmentPeriod('monthly');
        $loan->setInterestRate($data['interestRate']);
        $loan->setAmountRaised($data['amountRaised']);

        $loan->setInstallmentDay($data['installmentDay']);
        $loan->setApplicationDate(new \DateTime());
        $loan->calculateInstallmentCount(Money::create($data['installmentAmount'], $currency));

        return $loan;
    }

    public function getNativeAmount()
    {
        return Money::create(parent::getnativeAmount(), $this->getCurrencyCode());
    }

    public function setNativeAmount($money)
    {
        return parent::setNativeamount($money->getAmount());
    }

    public function getInstallmentAmount()
    {
        return Money::create(parent::getInstallmentAmount(), $this->getCurrencyCode());
    }

    public function setInstallmentAmount($money)
    {
        return parent::setInstallmentAmount($money->getAmount());
    }

    public function getAmount()
    {
        return Money::create(parent::getAmount(), 'USD');
    }

    public function setAmount($money)
    {
        return parent::setAmount($money->getAmount());
    }

    public function calculateExtraDays($disbursedDate)
    {
        $date = Carbon::instance($disbursedDate);
        
        if ($this->isWeeklyInstallment()) {
            $extraDays = ($this->getInstallmentDay() - $date->dayOfWeek + 7) % 7;
        } else {
            if ($date->day > $this->getInstallmentDay()) {
                // TODO installmentDay = 15, date = jan 30 => march 2 => march 15 => extra days > month
                $date->addMonth();
            }
            $date->day($this->getInstallmentDay());
            $extraDays = $date->diffInDays(Carbon::instance($disbursedDate));
        }
        
        return $this->setExtraDays($extraDays);
    }

    public function calculateInstallmentCount(Money $nativeInstallmentAmount)
    {
        $maxYearlyInterest = $this->getNativeAmount()->multiply($this->getInterestRate() / 100);
        
        if ($this->isWeeklyInstallment()) {
            $maxInstallmentInterest = $maxYearlyInterest->divide(52);
        } else {
            $maxInstallmentInterest = $maxYearlyInterest->divide(12);
        }
        
        $maxNativeInstallmentAmount = $nativeInstallmentAmount->subtract($maxInstallmentInterest);
        
        $installmentCount = ceil($this->getNativeAmount()->getAmount() / $maxNativeInstallmentAmount->getAmount());

        return $this->setInstallmentCount($installmentCount);
    }

    public function calculateAmountRaised(Money $totalBidAmount)
    {
        if ($totalBidAmount->lessThan($this->getAmount())) {
            $percentAmountRaised = $totalBidAmount->divide($this->getAmount())->multiply(100)->round(2)->getAmount();
        } else {
            $percentAmountRaised = 100;
        }

        return $this->setAmountRaised($percentAmountRaised);
    }

    public function isWeeklyInstallment()
    {
        return $this->getInstallmentPeriod() == self::WEEKLY_INSTALLMENT;
    }

    public function getYearlyInterestRateRatio()
    {
        if ($this->isWeeklyInstallment()) {
            $totalTimeLoanInWeeks = $this->getInstallmentCount() + round($this->getExtraDays() / 7, 4);
            return $totalTimeLoanInWeeks / 52;
        }

        $totalTimeLoanInMonths = $this->getInstallmentCount() + round($this->getExtraDays() / 30, 4);
        return $totalTimeLoanInMonths / 12;
    }

    public function getNativeLenderInterest()
    {
        return $this->getNativeAmount()->multiply($this->getYearlyInterestRateRatio() * $this->getFinalInterestRate() / 100);
    }
    
    public function getNativeRegistrationFee()
    {
        return $this->getNativeAmount()->multiply($this->getYearlyInterestRateRatio() * $this->getRegistrationFeeRate() / 100);
    }

    public function getNativeTotalInterest()
    {
        return $this->getNativeRegistrationFee()->add($this->getNativeLenderInterest());
    }

    public function getNativeTotalAmount()
    {
        return $this->getNativeAmount()->add($this->getNativeTotalInterest());
    }

    public function getNativeInstallmentAmount()
    {
        return $this->getNativeTotalAmount()->divide($this->getInstallmentCount());
    }

    public function getInstallmentGraceDate()
    {
        $date = Carbon::instance($this->getDisbursedDate());

        return $date->addDays($this->getExtraDays());
    }

    public function getNthInstallmentDate($n = 1)
    {
        $date = $this->getInstallmentGraceDate()->copy();
        
        if ($this->isWeeklyInstallment()) {
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
