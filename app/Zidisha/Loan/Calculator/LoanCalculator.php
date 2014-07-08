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

    public function maximumPeriod()
    {
        $maximumMonths = Setting::get('loan.maximumPeriod');
        if ($this->borrower->getCountry()->getInstallmentPeriod() == Loan::WEEKLY_INSTALLMENT) {
            $maximumMonths = ceil($maximumMonths / 12 * 52);
        }
        
        return $maximumMonths;
    }

}
