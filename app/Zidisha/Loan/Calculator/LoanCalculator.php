<?php

namespace Zidisha\Loan\Calculator;


use Zidisha\Borrower\Borrower;
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

    public function __construct(Borrower $borrower)
    {
        $this->borrower = $borrower;
        $this->currency = $borrower->getCountry()->getCurrency();
    }

    public function minimumAmount()
    {
        // TODO from config, minBorrowerAmt + exchange rate
        return Money::create(50, $this->currency);
    }

    public function maximumAmount()
    {
        // TODO getCurrentCreditLimit
        return Money::create(10000, $this->currency);
    }

    public function maximumPeriod()
    {
        // TODO config maxPeriodValue
        $maximumMonths = 24;
        if ($this->borrower->getCountry()->getInstallmentPeriod() == Loan::WEEKLY_INSTALLMENT) {
            $maximumMonths = ceil($maximumMonths / 12 * 52);
        }
        
        return $maximumMonths;
    }

}
