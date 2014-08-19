<?php

namespace Integration\Zidisha\Borrower;

use Carbon\Carbon;
use Propel\Runtime\ActiveQuery\Criteria;
use ReflectionMethod;
use Zidisha\Admin\Setting;
use Zidisha\Borrower\Borrower;
use Zidisha\Currency\Converter;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;

class BorrowerServiceTest extends \IntegrationTestCase
{
    /** @var  Borrower $borrower */
    protected $borrower;
    /** @var  Loan $loan */
    protected $loan;
    /** @var  Loan $secondLoan */
    protected $secondLoan;
    private $borrowerService;

    public function setUp()
    {
        parent::setUp();
        $this->borrowerService = $this->app->make('Zidisha\Borrower\BorrowerService');

        $this->borrower = \Zidisha\Generate\BorrowerGenerator::create()
            ->size(1)
            ->generate();

        $this->loan = \Zidisha\Generate\LoanGenerator::create()
            ->amount(50)
            ->generateOne();

        $this->secondLoan = \Zidisha\Generate\LoanGenerator::create()
            ->amount(50)
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

    public function testGetPreviousLoanAmountPluralLoan()
    {
        $method = new ReflectionMethod($this->borrowerService, 'getPreviousLoanAmount');
        $method->setAccessible(true);
        $this->loan->setStatus(Loan::REPAID)
            ->setDisbursedAt(new Carbon('yesterday'))
            ->setDisbursedAmount($this->loan->getAmount());
        $this->loan->save();

        $exchangeRate = ExchangeRateQuery::create()
            ->findCurrent($this->borrower->getCountry()->getCurrency());
        $amountNative = LoanQuery::create()
            ->filterById($this->secondLoan->getId(), Criteria::NOT_EQUAL)
            ->getMaximumDisbursedAmount($this->borrower, $this->loan->getCurrencyCode());

        $loanAmount = $method->invoke($this->borrowerService, $this->borrower, $this->secondLoan, $exchangeRate);

        $this->assertEquals($loanAmount, $amountNative);
    }

    public function testGetPreviousLoanAmountPluralLoanWithActiveLoan()
    {
        $method = new ReflectionMethod($this->borrowerService, 'getPreviousLoanAmount');
        $method->setAccessible(true);
        $this->loan->setStatus(Loan::ACTIVE);
        $this->loan->save();

        $exchangeRate = ExchangeRateQuery::create()
            ->findCurrent($this->borrower->getCountry()->getCurrency());
        $firstLoanValue = Money::create(Setting::get('loan.firstLoanValue'), 'USD');
        $currency = $this->loan->getCurrency();
        $firstLoanValueNative = Money::create($firstLoanValue, $currency, $exchangeRate);

        $loanAmount = $method->invoke($this->borrowerService, $this->borrower, $this->secondLoan, $exchangeRate);

        $this->assertEquals($loanAmount, $firstLoanValueNative);
    }
}
