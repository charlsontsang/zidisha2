<?php

class LendController extends BaseController
{

    protected $loanCategoryQuery;
    protected $CountryQuery;
    protected $loanQuery;

    public function  __construct(
        Zidisha\Loan\CategoryQuery $loanCategoryQuery,
        Zidisha\Country\CountryQuery $countryQuery,
        Zidisha\Loan\LoanQuery $loanQuery
    ) {
        $this->loanCategoryQuery = $loanCategoryQuery;
        $this->countryQuery = $countryQuery;
        $this->loanQuery = $loanQuery;
    }

    public function getIndex()
    {
        // for categories
        $loanCategories = $this->loanCategoryQuery
            ->orderByRank()
            ->find();

        //for countries
        $countries = $this->countryQuery
            ->orderByName()
            ->find();

        //for loans
        $loanQuery = $this->loanQuery->orderBySummary();

        $loanCategoryId = Request::query('loan_category_id');
        $selectedLoanCategory = $this->loanCategoryQuery
            ->findOneById($loanCategoryId);

        if ($selectedLoanCategory) {
            $loanQuery->filterByLoanCategoryId($loanCategoryId);
        }

        $countryId = Request::query('country_id');
        $selectedCountry = $this->countryQuery->findOneById($countryId);

        if($selectedCountry){
            $loanQuery
                ->useBorrowerQuery()
                    ->filterByCountryId($countryId)
                ->endUse();
        }

        $loans = $loanQuery->find();

        return View::make(
            'pages.lend',
            compact('countries', 'selectedCountry', 'loanCategories', 'selectedLoanCategory', 'loans')
        );

    }
}