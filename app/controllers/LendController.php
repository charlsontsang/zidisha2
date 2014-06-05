<?php

class LendController extends BaseController
{

    public function getIndex()
    {
        // for categories
        $loanCategories = LoanCategoryQuery::create()
            ->orderByRank()
            ->find();

        $loanCategoryId = Request::query('loan_category_id');
        // TODO
        $selectedLoanCategory = LoanCategoryQuery::create()->findOneById($loanCategoryId);

        $countries = CountryQuery::create()
            ->orderByName()
            ->find();

        $countryId = Request::query('country_id');
        // TODO
        $selectedCountry = CountryQuery::create()->findOneById($countryId);

        return View::make(
            'pages.lend',
            compact('countries', 'selectedCountry', 'loanCategories', 'selectedLoanCategory')
        );

    }
}

?>