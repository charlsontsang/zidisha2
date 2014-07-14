<?php

namespace Zidisha\Loan;

use Carbon\Carbon;
use Zidisha\Currency\Currency;
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
    
    const WEEKLY_INSTALLMENT  = 'weekly';
    const MONTHLY_INSTALLMENT = 'monthly';

    public static function createFromData($data)
    {
        $currency = $data['currencyCode'];

        $loan = new Loan();
        $loan->setSummary($data['summary']);
        $loan->setProposal($data['proposal']);

        $loan->setCurrencyCode($data['currencyCode']);
        $loan->setNativeAmount(Money::create($data['nativeAmount'], $currency));

        $loan->setAmount(Money::create($data['amount'], 'USD'));
        $loan->setRegistrationFeeRate('5');
        $loan->setInstallmentPeriod('monthly'); // TODO $borrower->getCountry()->getInstallmentPeriod()
        $loan->setInterestRate(20); // TODO

        $loan->setInstallmentDay($data['installmentDay']);
        $loan->setApplicationDate(new \DateTime());
        $loan->calculateInstallmentCount(Money::create($data['installmentAmount'], $currency));

        return $loan;
    }

    /**
     * @return Money
     */
    public function getNativeAmount()
    {
        return Money::create(parent::getNativeAmount(), $this->getCurrencyCode());
    }

    /**
     * @param Money $money
     * @return $this|Loan
     */
    public function setNativeAmount($money)
    {
        return parent::setNativeAmount($money->getAmount());
    }

    /**
     * @return Money
     */
    public function getAmount()
    {
        return Money::create(parent::getAmount(), 'USD');
    }

    /**
     * @param Money $money
     * @return $this|Loan
     */
    public function setAmount($money)
    {
        return parent::setAmount($money->getAmount());
    }

    public function getCurrency()
    {
        return Currency::valueOf($this->getCurrencyCode());
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

    /**
     * @param Money $totalBidAmount
     * @return $this|Loan
     */
    public function setRaisedAmount($totalBidAmount)
    {
        if ($totalBidAmount->lessThan($this->getAmount())) {
            $raisedPercentage = $totalBidAmount
                ->divide($this->getAmount()->getAmount())
                ->multiply(100)->round(2)
                ->getAmount();
        } else {
            $raisedPercentage = 100;
        }

        $this->setRaisedPercentage($raisedPercentage);
        return parent::setRaisedAmount($raisedPercentage);
    }

    /**
     * @return Money
     */
    public function getRaisedAmount()
    {
        return Money::create(parent::getRaisedAmount(), 'USD');
    }
    
    public function getStillNeededAmount()
    {
        return $this->getAmount()->subtract($this->getRaisedAmount())->max(Money::create(0));
    }

    public function isWeeklyInstallment()
    {
        return $this->getInstallmentPeriod() == self::WEEKLY_INSTALLMENT;
    }

    public function isOpen()
    {
        return $this->getStatus() == static::OPEN;
    }
    
    public function isActive()
    {
        return $this->getStatus() == static::ACTIVE;
    }

    public function isFullyFunded()
    {
        return !$this->getAmount()->greaterThan($this->getRaisedAmount());
    }
}
