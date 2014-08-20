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
    private $borrowerService;

    public function setUp()
    {
        parent::setUp();
        $this->borrowerService = $this->app->make('Zidisha\Borrower\BorrowerService');

        $this->borrower = \Zidisha\Generate\BorrowerGenerator::create()
            ->size(1)
            ->generate();

    }

    public function testGetPreviousLoanAmountFirstLoan()
    {
        $method = new ReflectionMethod($this->borrowerService, 'getPreviousLoanAmount');
        $method->setAccessible(true);

        /** @var $loan Loan */
        $loan = \Zidisha\Generate\LoanGenerator::create()
            ->amount(50)
            ->generateOne();


        $exchangeRate = ExchangeRateQuery::create()
            ->findCurrent($this->borrower->getCountry()->getCurrency());
        $firstLoanValue = Money::create(Setting::get('loan.firstLoanValue'), 'USD');
        $currency = $loan->getCurrency();
        $firstLoanValueNative = Money::create($firstLoanValue, $currency, $exchangeRate);

        $loanAmount = $method->invoke($this->borrowerService, $this->borrower, $loan, $exchangeRate);

        $this->assertEquals($loanAmount, $firstLoanValueNative);
    }

    public function testGetPreviousLoanAmountPluralLoan()
    {
        /** @var $loan Loan */
        $loan = \Zidisha\Generate\LoanGenerator::create()
            ->amount(50)
            ->generateOne();

        /** @var $secondLoan Loan */
        $secondLoan = \Zidisha\Generate\LoanGenerator::create()
            ->amount(50)
            ->generateOne();

        $method = new ReflectionMethod($this->borrowerService, 'getPreviousLoanAmount');
        $method->setAccessible(true);
        $loan->setStatus(Loan::REPAID)
            ->setDisbursedAt(new Carbon('yesterday'))
            ->setDisbursedAmount($loan->getAmount());
        $loan->save();

        $exchangeRate = ExchangeRateQuery::create()
            ->findCurrent($this->borrower->getCountry()->getCurrency());
        $amountNative = LoanQuery::create()
            ->filterById($secondLoan->getId(), Criteria::NOT_EQUAL)
            ->getMaximumDisbursedAmount($this->borrower, $loan->getCurrencyCode());

        $loanAmount = $method->invoke($this->borrowerService, $this->borrower, $secondLoan, $exchangeRate);

        $this->assertEquals($loanAmount, $amountNative);
    }

    public function testGetPreviousLoanAmountPluralLoanWithActiveLoan()
    {
        /** @var $loan Loan */
        $loan = \Zidisha\Generate\LoanGenerator::create()
            ->amount(50)
            ->generateOne();

        /** @var $secondLoan Loan */
        $secondLoan = \Zidisha\Generate\LoanGenerator::create()
            ->amount(50)
            ->generateOne();

        $method = new ReflectionMethod($this->borrowerService, 'getPreviousLoanAmount');
        $method->setAccessible(true);
        $loan->setStatus(Loan::ACTIVE);
        $loan->save();

        $exchangeRate = ExchangeRateQuery::create()
            ->findCurrent($this->borrower->getCountry()->getCurrency());
        $firstLoanValue = Money::create(Setting::get('loan.firstLoanValue'), 'USD');
        $currency = $loan->getCurrency();
        $firstLoanValueNative = Money::create($firstLoanValue, $currency, $exchangeRate);

        $loanAmount = $method->invoke($this->borrowerService, $this->borrower, $secondLoan, $exchangeRate);

        $this->assertEquals($loanAmount, $firstLoanValueNative);
    }

    public function testGetCurrentCreditLimitForFirstLoanWithRequestAmountLess()
    {
        $method = new ReflectionMethod($this->borrowerService, 'getCurrentCreditLimit');
        $method->setAccessible(true);
        /** @var $loan Loan */
        $loan = \Zidisha\Generate\LoanGenerator::create()
            ->amount(50)
            ->generateOne();

        $exchangeRate = ExchangeRateQuery::create()
            ->findCurrent($this->borrower->getCountry()->getCurrency());
        $firstLoanValue = Money::create(Setting::get('loan.firstLoanValue'), 'USD');
        $currency = $loan->getCurrency();
        $firstLoanValueNative = Converter::fromUSD($firstLoanValue, $currency, $exchangeRate);
        $creditEarned = Money::create(550, $this->borrower->getCountry()->getCurrencyCode(), $exchangeRate);

        $creditLimit = $method->invoke($this->borrowerService, $this->borrower, $creditEarned, false);

        $this->assertEquals($creditLimit, $firstLoanValueNative);
    }

    public function testGetCurrentCreditLimitForFirstLoanWithRequestAmountMore()
    {
        $method = new ReflectionMethod($this->borrowerService, 'getCurrentCreditLimit');
        $method->setAccessible(true);

        /** @var $loan Loan */
        $loan = \Zidisha\Generate\LoanGenerator::create()
            ->amount(100)
            ->generateOne();

        $exchangeRate = ExchangeRateQuery::create()
            ->findCurrent($this->borrower->getCountry()->getCurrency());
        $currency = $loan->getCurrency();
        $raisedUsdAmount = $loan->getRaisedUsdAmount();
        $raisedAmount = Converter::fromUSD($raisedUsdAmount, $currency, $exchangeRate);
        $creditEarned = Money::create(550, $this->borrower->getCountry()->getCurrencyCode(), $exchangeRate);

        $creditLimit = $method->invoke($this->borrowerService, $this->borrower, $creditEarned, false);

        $this->assertEquals($creditLimit, $raisedAmount->add($creditEarned));
    }

    public function testGetCurrentCreditLimitForPluralLoanWithLoanNotHeldLongEnough()
    {
        /** @var $loan Loan */
        $loan = \Zidisha\Generate\LoanGenerator::create()
            ->amount(50)
            ->generateOne();

        /** @var $secondLoan Loan */
        $secondLoan = \Zidisha\Generate\LoanGenerator::create()
            ->amount(50)
            ->generateOne();

        $method = new ReflectionMethod($this->borrowerService, 'getCurrentCreditLimit');
        $method->setAccessible(true);
        $secondLoan->setStatus(Loan::ACTIVE)
            ->setDisbursedAt(new Carbon('yesterday'))
            ->setRaisedUsdAmount($secondLoan->getUsdAmount())
            ->setDisbursedAmount($secondLoan->getAmount());
        $secondLoan->save();
        $this->borrower->setActiveLoan($secondLoan);
        $this->borrower->save();

        $exchangeRate = ExchangeRateQuery::create()
            ->findCurrent($this->borrower->getCountry()->getCurrency());
        $currency = $loan->getCurrency();
        $raisedUsdAmount = $secondLoan->getRaisedUsdAmount();
        $raisedAmount = Converter::fromUSD($raisedUsdAmount, $currency, $exchangeRate);
        $creditEarned = Money::create(550, $this->borrower->getCountry()->getCurrencyCode(), $exchangeRate);
        $raisedAmountAddCredit = Converter::fromUSD($raisedUsdAmount, $currency, $exchangeRate)->add($creditEarned);

        $creditLimit = $method->invoke($this->borrowerService, $this->borrower, $creditEarned, false);
        $creditLimitAddCredit = $method->invoke($this->borrowerService, $this->borrower, $creditEarned, true);

        $this->assertEquals($creditLimit, $raisedAmount);
        $this->assertEquals($creditLimitAddCredit, $raisedAmountAddCredit);
    }

    public function testGetCurrentCreditLimitForPluralLoanWithLoanHeldLongEnough()
    {
        /** @var $loan Loan */
        $loan = \Zidisha\Generate\LoanGenerator::create()
            ->amount(50)
            ->generateOne();

        /** @var $secondLoan Loan */
        $secondLoan = \Zidisha\Generate\LoanGenerator::create()
            ->amount(50)
            ->generateOne();

        $method = new ReflectionMethod($this->borrowerService, 'getCurrentCreditLimit');
        $method->setAccessible(true);
        $disburseDate = Carbon::createFromDate(null, 5, 25);
        $secondLoan->setStatus(Loan::ACTIVE)
            ->setDisbursedAt($disburseDate)
            ->setRaisedUsdAmount($secondLoan->getUsdAmount())
            ->setDisbursedAmount($secondLoan->getAmount());
        $secondLoan->save();
        $this->borrower->setActiveLoan($secondLoan);
        $this->borrower->save();

        $exchangeRate = ExchangeRateQuery::create()
            ->findCurrent($this->borrower->getCountry()->getCurrency());
        $currency = $loan->getCurrency();
        $raisedUsdAmount = $secondLoan->getRaisedUsdAmount();
        $raisedAmount = Converter::fromUSD($raisedUsdAmount, $currency, $exchangeRate);
        $percentIncrease = Setting::get('loan.secondLoanPercentage');
        $amount = $raisedAmount->multiply($percentIncrease)->divide(100);
        $creditEarned = Money::create(550, $this->borrower->getCountry()->getCurrencyCode(), $exchangeRate);
        $amountAddCredit = $raisedAmount->multiply($percentIncrease)->divide(100)->add($creditEarned);

        $creditLimit = $method->invoke($this->borrowerService, $this->borrower, $creditEarned, false);
        $creditLimitAddCredit = $method->invoke($this->borrowerService, $this->borrower, $creditEarned, true);

        $this->assertEquals($creditLimit, $amount);
        $this->assertEquals($creditLimitAddCredit, $amountAddCredit);
    }
}
