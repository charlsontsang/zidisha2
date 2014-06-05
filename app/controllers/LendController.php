<?php

class LendController extends BaseController
{

    public function getIndex()
    {
        $loanCategories = LoanCategoryQuery::create()
            ->orderByRank()
            ->find();

        $loanCategoryId = Request::query('loan_category_id');
        // TODO
        $selectedLoanCategory = LoanCategoryQuery::create()->findOneById($loanCategoryId);

        return View::make('pages.lend', compact('loanCategories', 'selectedLoanCategory'));
    }
}

?>