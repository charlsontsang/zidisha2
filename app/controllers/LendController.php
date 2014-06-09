<?php

class LendController extends BaseController
{

    protected $loanCategoryQuery;
    protected $CountryQuery;
    protected $loanQuery;

    public function  __construct(
        Zidisha\Loan\LoanCategoryQuery $loanCategoryQuery,
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

        $loanCategoryId = Request::query('loan_category_id');
        // TODO
        $selectedLoanCategory = $this->loanCategoryQuery
            ->findOneById($loanCategoryId);

        //for countries
        $countries = $this->countryQuery
            ->orderByName()
            ->find();

        $countryId = Request::query('country_id');
        // TODO
        $selectedCountry = $this->countryQuery->findOneById($countryId);

        //for loans
        $loans = $this->loanQuery
            ->orderBySummary()
            ->filterByLoanCategoryId($loanCategoryId)
            ->find();

        return View::make(
            'pages.lend',
            compact('countries', 'selectedCountry', 'loanCategories', 'selectedLoanCategory', 'loans')
        );

    }
}