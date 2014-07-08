<?php

namespace Zidisha\Borrower\Form\Loan;

use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\Form\Validator\LoanValidator;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Form\AbstractForm;
use Zidisha\Loan\Calculator\LoanCalculator;
use Zidisha\Loan\CategoryQuery;
use Zidisha\Loan\CategoryTranslation;
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

        $language = \Auth::user()->getBorrower()->getCountry()->getLanguage();
        $values = [];

            $categories = CategoryQuery::create()
                ->orderBySortableRank()
                ->findByAdminOnly(false);

        if($language->getLanguageCode() == 'IN' || $language->getLanguageCode() == 'FR')
        {
            foreach($categories as $category){
                $translation = CategoryTranslationQuery::create()
                    ->filterByCategory($category)
                    ->filterByLanguage($language)
                    ->findOne();

                $values[$category->getId()] = $translation->getTranslation();
            }

            return $values;
        }

        return $categories->toKeyValue('id', 'name');
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

    public function getDefaultData()
    {
        return \Session::get('loan_data');
    }

    protected function validate($data, $rules)
    {
        \Validator::resolver(
            function ($translator, $data, $rules, $messages, $parameters) {
                return new LoanValidator($translator, $data, $rules, $messages, $parameters);
            }
        );

        parent::validate($data, $rules);
    }
}