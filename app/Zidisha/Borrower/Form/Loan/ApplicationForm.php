<?php

namespace Zidisha\Borrower\Form\Loan;

use Zidisha\Borrower\Borrower;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Currency\Money;
use Zidisha\Form\AbstractForm;
use Zidisha\Loan\Calculator\LoanCalculator;
use Zidisha\Loan\CategoryQuery;
use Zidisha\Loan\CategoryTranslationQuery;
use Zidisha\Loan\Loan;

class ApplicationForm extends AbstractForm
{
    /**
     * @var \Zidisha\Borrower\Base\Borrower
     */
    protected  $borrower;

    /**
     * @var \Zidisha\Loan\Calculator\LoanCalculator
     */
    protected $loanCalculator;

    /**
     * @var \Zidisha\Currency\Currency
     */
    protected $currency;

    /**
     * @var mixed|\Zidisha\Currency\ExchangeRate
     */
    protected $exchangeRate;

    public function __construct(Borrower $borrower)
    {
        $this->borrower = $borrower;
        $this->currency = $this->borrower->getCountry()->getCurrency();
        $this->exchangeRate = ExchangeRateQuery::create()->findCurrent($this->currency);
        $this->loanCalculator = new LoanCalculator($borrower, $this->exchangeRate);
    }
    
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getRules($data)
    {
        if (isset($data['amount'])) {
            $amount = Money::create($data['amount'], $this->currency);
        } else {
            $amount = $this->loanCalculator->maximumAmount()->getAmount();
        }
        
        $amounts = implode(',', $this->getLoanAmountRange());
        $installmentAmounts = implode(',', $this->getInstallmentAmountRange($amount));
        $days = implode(',', $this->getDays());
        
        return [
            'summary'           => 'required|min:1|max:60', 
            'proposal'          => 'required|min:200',
            'categoryId'        => 'required|exists:loan_categories,id,admin_only,false',
            'amount'            => "required|numeric|in:$amounts",
            'installmentAmount' => "required|numeric|greaterThan:0|in:$installmentAmounts",
            'installmentDay'    => "required|in:$days",
        ];
    }

    public function getCategories()
    {
        $languageCode = $this->borrower->getCountry()->getLanguageCode();

        $categories = CategoryQuery::create()
            ->orderBySortableRank()
            ->findByAdminOnly(false);
        $values = $categories->toKeyValue('id', 'name');

        if ($languageCode != 'EN') {
            $translations = CategoryTranslationQuery::create()
                ->filterByLanguageCode($languageCode)
                ->find();

            $values = $translations->toKeyValue('categoryId', 'translation') + $values;
        }

        return $values;
    }

    protected function getDaysInPeriod()
    {
        $installmentPeriod = $this->borrower->getCountry()->getInstallmentPeriod();
        
        return ($installmentPeriod == Loan::WEEKLY_INSTALLMENT) ? 7 : 31;
    }
    
    public function getDays()
    {   
        $array = range(1, $this->getDaysInPeriod());

        return array_combine($array, $array);
    }
    
    public function getLoanAmountRange() {
        $step = $this->borrower->getCountry()->getLoanAmountStep();
        
        $range = range($this->loanCalculator->maximumAmount()->getAmount(), $step, $step);
        
        return array_combine($range, $range);
    }

    public function getInstallmentAmountRange(Money $amount = null) {
        $step = $this->borrower->getCountry()->getInstallmentAmountStep();

        $amount = $amount ?: $this->getAmount();
        $minInstallmentAmount = $this->loanCalculator->minInstallmentAmount($amount);
        $minInstallmentAmount = $minInstallmentAmount->divide($step)->ceil()->multiply($step);
        
        $maxInstallmentAmount = $amount->divide($this->loanCalculator->minimumPeriod($amount));

        if ($step > $maxInstallmentAmount->getAmount()) {
            return [];
        }
        
        $range = range($maxInstallmentAmount->getAmount(), $minInstallmentAmount->getAmount(), $step);

        return array_combine($range, $range);
    }

    public function getDefaultData()
    {
        return \Session::get('loan_data', []);
    }

    protected function getAmount()
    {
        // Submitted input value
        if (\Session::has('_old_input.amount')) {
            $amount = Money::create(\Session::get('_old_input.amount'), $this->currency);
        } elseif (\Session::has('loan_data.amount')) {
            $amount = Money::create(\Session::get('loan_data.amount'), $this->currency);
        } else {
            $amount = $this->loanCalculator->maximumAmount();
        }
        
        return $amount;
    }

    public function isValidAmount($amount)
    {
        $data = compact('amount');
        $rules = $this->getRules($data);
        $validator = \Validator::make($data, ['amount' => $rules['amount']]);
        
        return $validator->passes();
    }
}