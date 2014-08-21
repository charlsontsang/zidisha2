<?php

namespace Zidisha\Generate;


use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Currency\Converter;
use Zidisha\Currency\CurrencyService;
use Zidisha\Currency\Money;
use Zidisha\Loan\CategoryQuery;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanService;

class LoanGenerator extends Generator
{
    protected $categoryIds;
    protected $borrowers;
    
    protected $appliedAtStartDate = '- 16 months';
    protected $appliedAtEndDate = 'now';
    protected $minAmount = 50;
    protected $maxAmount = 400;
    /**
     * @var \Zidisha\Loan\LoanService
     */
    private $loanService;
    /**
     * @var \Zidisha\Currency\CurrencyService
     */
    private $currencyService;

    public function __construct(LoanService $loanService, CurrencyService $currencyService)
    {
        $this->loanService = $loanService;
        $this->currencyService = $currencyService;
    }

    public function appliedAtBetween($startDate = '- 16 months', $endDate = 'now')
    {
        $this->appliedAtStartDate = $startDate;
        $this->appliedAtEndDate = $endDate;

        return $this;
    }

    public function amountBetween($min = 50, $max = 400)
    {
        $this->minAmount = $min;
        $this->maxAmount = $max;

        return $this;
    }

    public function amount($amount)
    {
        $this->amountBetween($amount, $amount);
        
        return $this;
    }

    protected function beforeGenerate()
    {
        $this->categoryIds = CategoryQuery::create()
            ->filterByAdminOnly(false)
            ->orderByRank()
            ->select('id')
            ->find()
            ->getData();

        $this->borrowers = BorrowerQuery::create()
            ->filterByActive(true)
            ->filterByActivationStatus(Borrower::ACTIVATION_APPROVED)
            ->joinWith('Country')
            ->orderById()
            ->find()
            ->getData();

        if (!$this->categoryIds) {
            throw new \Exception("Not enough categories");
        }

        if (count($this->borrowers) < $this->size) {
            throw new \Exception("Not enough borrowers");
        }
    }

    protected function doGenerate($i)
    {
        /** @var Borrower $borrower */
        $borrower = $this->borrowers[$i-1];
        $currency = $borrower->getCountry()->getCurrency();

        $date = $this->faker->dateTimeBetween($this->appliedAtStartDate, $this->appliedAtEndDate);
        $exchangeRate = $this->currencyService->getExchangeRate($currency, $date);
        $usdAmount = Money::create($this->faker->numberBetween($this->minAmount, $this->maxAmount));
        $amount = Converter::fromUSD($usdAmount, $currency, $exchangeRate);

        $isWeekly = $borrower->getCountry()->getInstallmentPeriod() == Loan::WEEKLY_INSTALLMENT;

        $data = [
            'summary'           => $this->faker->sentence(8),
            'proposal'          => $this->faker->paragraph(7),
            'amount'            => $amount->getAmount(),
            'installmentAmount' => $amount->divide($this->faker->numberBetween(6, 16))->getAmount(),
            'currencyCode'      => $borrower->getCountry()->getCurrencyCode(),
            'installmentDay'    => $isWeekly ? $this->faker->dayOfWeek : $this->faker->dayOfMonth,
            'date'              => $date,
            'exchangeRate'      => $exchangeRate,
            'categoryId'        => $this->faker->randomElement($this->categoryIds),
        ];

        $loan = $this->loanService->applyForLoan($borrower, $data);
        
        return $loan;
    }
}
