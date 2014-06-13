<?php

use Zidisha\Borrower\BorrowerQuery;

class LoanController extends BaseController
{

    protected $loanQuery;

    public function  __construct(
        Zidisha\Loan\LoanQuery $loanQuery
    ) {

        $this->loanQuery = $loanQuery;
    }

    public function getIndex($loanId)
    {

        //for loan
        $loan = $this->loanQuery
            ->filterById($loanId)
            ->findOne();

        return View::make(
            'pages.loan',
            compact('loan', 'borrower')
        );
    }
}