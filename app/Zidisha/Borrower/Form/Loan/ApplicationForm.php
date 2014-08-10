<?php

namespace Zidisha\Borrower\Form\Loan;

use Zidisha\Borrower\Borrower;
use Zidisha\Currency\ExchangeRateQuery;
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

    protected $validatorClass = 'Zidisha\Borrower\Form\Validator\LoanValidator';

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

    public function getRules($data)
    {
        $minimumAmount = $this->loanCalculator->minimumAmount()->getAmount();
        $maximumAmount = $this->loanCalculator->maximumAmount()->getAmount();
        $maximumPeriod = $this->loanCalculator->maximumPeriod();
        $period = $this->borrower->getCountry()->getInstallmentPeriod() == Loan::WEEKLY_INSTALLMENT ? 'weeks' : 'months';
        $amount = array_get($data, 'amount', $minimumAmount);
        $days = implode(',', $this->getDays());
        
        return [
            'summary'           => 'required|min:10', // TODO max
            'proposal'          => 'required|min:200',
            'categoryId'        => 'required|exists:loan_categories,id,admin_only,false',
            'amount'            => "required|numeric|between:$minimumAmount,$maximumAmount",
            'installmentAmount' => "required|numeric|greaterThan:0|minimumInstallmentAmount:$maximumPeriod,$period|max:$amount",
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

        if ($languageCode != 'EN')
        {
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
        
        return range($this->loanCalculator->maximumAmount()->getAmount(), $step, $step);
    }

    public function getInstallmentAmountRange() {
        $step = $this->borrower->getCountry()->getInstallmentAmountStep();
        
        $minInstallmentAmount = $this->loanCalculator->minInstallmentAmount();
        $minInstallmentAmount = $minInstallmentAmount->divide($step)->ceil()->multiply($step);
        
        $maxInstallmentAmount = $this->loanCalculator->maximumAmount()->divide($this->loanCalculator->minimumPeriod());

        return range($maxInstallmentAmount->getAmount(), $minInstallmentAmount->getAmount(), $step);
    }

    public function getDefaultData()
    {
        return \Session::get('loan_data');
    }
}