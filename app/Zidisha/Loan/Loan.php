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
        $loan->setAmount(Money::create($data['amount'], $currency));

        $loan->setUsdAmount(Money::create($data['usdAmount'], 'USD'));
        $loan->setInstallmentPeriod('monthly'); // TODO $borrower->getCountry()->getInstallmentPeriod()
        $loan->setInterestRate(20); // TODO

        $loan->setInstallmentDay($data['installmentDay']);
        $loan->setAppliedAt(new \DateTime());
        $loan->calculateInstallmentCount(Money::create($data['installmentAmount'], $currency));

        return $loan;
    }

    /**
     * @return Money
     */
    public function getUsdAmount()
    {
        return Money::create(parent::getUsdAmount(), 'USD');
    }

    /**
     * @param Money $money
     * @return $this|Loan
     */
    public function setUsdAmount($money)
    {
        return parent::setUsdAmount($money->getAmount());
    }

    /**
     * @return Money
     */
    public function getAmount()
    {
        return Money::create(parent::getAmount(), $this->getCurrencyCode());
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

    /**
     * @param Money $raisedUsdAmount
     * @return $this|Loan
     */
    public function setRaisedUsdAmount($raisedUsdAmount)
    {
        if ($raisedUsdAmount->lessThan($this->getUsdAmount())) {
            $raisedPercentage = $raisedUsdAmount
                ->divide($this->getUsdAmount()->getAmount())
                ->multiply(100)->round(2)
                ->getAmount();
        } else {
            $raisedPercentage = 100;
        }
        
        $this->setRaisedPercentage($raisedPercentage);
        return parent::setRaisedUsdAmount($raisedUsdAmount->getAmount());
    }

    /**
     * @return Money
     */
    public function getRaisedUsdAmount()
    {
        return Money::create(parent::getRaisedUsdAmount(), 'USD');
    }
    
    public function getStillNeededUsdAmount()
    {
        return $this->getUsdAmount()->subtract($this->getRaisedUsdAmount())->max(Money::create(0));
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
        return !$this->getAmount()->greaterThan($this->getRaisedUsdAmount());
    }
}
