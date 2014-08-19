<?php

namespace Integration\Zidisha\Borrower;

use ReflectionMethod;
use Zidisha\Admin\Setting;
use Zidisha\Borrower\Borrower;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;

class BorrowerServiceTest extends \IntegrationTestCase
{
    /** @var  Borrower $borrower */
    protected $borrower;
    /** @var  Loan $loan */
    protected $loan;
    private $borrowerService;

    public function setUp()
    {
        parent::setUp();
        $this->borrowerService = $this->app->make('Zidisha\Borrower\BorrowerService');

        $this->borrower = \Zidisha\Generate\BorrowerGenerator::create()
            ->size(1)
            ->generate();

        $this->loan = \Zidisha\Generate\LoanGenerator::create()
            ->amount(1)
            ->generateOne();


    }

    public function testGetPreviousLoanAmountFirstLoan()
    {
        $method = new ReflectionMethod($this->borrowerService, 'getPreviousLoanAmount');
        $method->setAccessible(true);

        $exchangeRate = ExchangeRateQuery::create()
            ->findCurrent($this->borrower->getCountry()->getCurrency());
        $firstLoanValue = Money::create(Setting::get('loan.firstLoanValue'), 'USD');
        $currency = $this->loan->getCurrency();
        $firstLoanValueNative = Money::create($firstLoanValue, $currency, $exchangeRate);

        $loanAmount = $method->invoke($this->borrowerService, $this->borrower, $this->loan, $exchangeRate);

        $this->assertEquals($loanAmount, $firstLoanValueNative);
    }
}
