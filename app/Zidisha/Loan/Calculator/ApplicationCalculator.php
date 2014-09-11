<?php

namespace Zidisha\Loan\Calculator;


use Zidisha\Admin\Setting;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\Calculator\CreditLimitCalculator;
use Zidisha\Currency\Converter;
use Zidisha\Currency\ExchangeRate;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;

class ApplicationCalculator
{
    /**
     * @var Borrower
     */
    protected $borrower;

    /**
     * @var \Zidisha\Currency\Currency
     */
    protected $currency;
    
    /**
     * @var \Zidisha\Currency\ExchangeRate
     */
    protected $exchangeRate;

    /**
     * @var \Zidisha\Borrower\Calculator\CreditLimitCalculator
     */
    protected $creditLimitCalculator;

    /**
     * @var Money
     */
    protected $maximumAmount;

    public function __construct(Borrower $borrower, ExchangeRate $exchangeRate)
    {
        $this->borrower = $borrower;
        $this->currency = $borrower->getCountry()->getCurrency();
        $this->exchangeRate = $exchangeRate;
        
        $this->creditLimitCalculator = new CreditLimitCalculator($borrower, $exchangeRate);
    }

    public function minimumAmount()
    {
        $amountUsd = Money::create(Setting::get('loan.minimumAmount'));
        $amount = Converter::fromUSD($amountUsd, $this->currency, $this->exchangeRate);
        
        return $amount->floor();
    }

    public function maximumAmount()
    {
        if ($this->maximumAmount === null) {
            $this->maximumAmount = $this->creditLimitCalculator->getCreditLimit($this->borrower);
        }
        
        return $this->maximumAmount;
    }

    public function minInstallmentAmount(Money $amount)
    {
        $period = $this->maximumPeriod();
        
        $annualRate = Setting::get('loan.maximumLenderInterestRate') + Setting::get('loan.transactionFeeRate');
        if ($this->borrower->getCountry()->getInstallmentPeriod() == Loan::WEEKLY_INSTALLMENT) {
            $interest = $amount->multiply($annualRate)->multiply($period)->divide(5200);
        } else {
            $interest = $amount->multiply($annualRate)->multiply($period)->divide(1200);
        }
        
        $totalAmount = $amount->add($interest);
        
        return $totalAmount->divide($period)->ceil();
    }

    public function minimumPeriod(Money $amount)
    {
        $amountUsd = Converter::toUSD($amount, $this->exchangeRate)->getAmount();
        
        if ($amountUsd <= 200) {
            $threshold = Setting::get('loan.loanIncreaseThresholdLow');
        } elseif ($amountUsd <= 1000) {
            $threshold = Setting::get('loan.loanIncreaseThresholdMid');
        } elseif ($amountUsd <= 3000) {
            $threshold = Setting::get('loan.loanIncreaseThresholdHigh');
        } else {
            $threshold = Setting::get('loan.loanIncreaseThresholdTop');
        }

        $minimumPeriod = $threshold;

        if ($this->borrower->getCountry()->getInstallmentPeriod() == Loan::WEEKLY_INSTALLMENT) {
            $minimumPeriod = ceil($threshold * (52/12));
        }
        
        return $minimumPeriod;
    }

    public function maximumPeriod()
    {
        $maximumMonths = Setting::get('loan.maximumPeriod');
        if ($this->borrower->getCountry()->getInstallmentPeriod() == Loan::WEEKLY_INSTALLMENT) {
            $maximumMonths = ceil($maximumMonths / 12 * 52);
        }
        
        return $maximumMonths;
    }

}