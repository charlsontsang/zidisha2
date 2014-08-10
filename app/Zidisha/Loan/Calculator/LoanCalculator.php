<?php

namespace Zidisha\Loan\Calculator;


use Zidisha\Admin\Setting;
use Zidisha\Borrower\Borrower;
use Zidisha\Currency\Converter;
use Zidisha\Currency\ExchangeRate;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;

class LoanCalculator
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
    private $exchangeRate;

    public function __construct(Borrower $borrower, ExchangeRate $exchangeRate)
    {
        $this->borrower = $borrower;
        $this->currency = $borrower->getCountry()->getCurrency();
        $this->exchangeRate = $exchangeRate;
    }

    public function minimumAmount()
    {
        $amountUsd = Money::create(Setting::get('loan.minimumAmount'));
        $amount = Converter::fromUSD($amountUsd, $this->currency, $this->exchangeRate);
        
        return $amount->floor();
    }

    public function maximumAmount()
    {
        // TODO getCurrentCreditLimit
        return Money::create(10000, $this->currency);
    }

    public function minInstallmentAmount()
    {
        // TODO fix period, grace period
        $amount = $this->maximumAmount();
        $period = $this->maximumPeriod();
        
        // 15% max interest
        if ($this->borrower->getCountry()->getInstallmentPeriod() == Loan::WEEKLY_INSTALLMENT) {
            $interest = $amount->multiply(15 * $period)->divide(5200);
        } else {
            $interest = $amount->multiply(15 * $period)->divide(1200);
        }
        
        $totalAmount = $amount->add($interest);
        $installmentAmount = $totalAmount->divide($period + 1)->ceil();
        
        return $installmentAmount;
    }

    public function minimumPeriod()
    {
        // TODO loanapplic_step3.php
        return 2;
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
