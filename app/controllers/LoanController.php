<?php

use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Comment\CommentService;
use Zidisha\Loan\LoanQuery;

class LoanController extends BaseController
{

    protected $loanQuery;
    /**
     * @var Zidisha\Comment\CommentService
     */
    private $commentService;

    public function  __construct(
        LoanQuery $loanQuery,
        CommentService $commentService
    ) {

        $this->loanQuery = $loanQuery;
        $this->commentService = $commentService;
    }

    public function getIndex($loanId)
    {
        //for loan
        $loan = $this->loanQuery
            ->filterById($loanId)
            ->findOne();

        $borrower = $loan->getBorrower();
        $comments = $this->commentService->getPaginatedComments($borrower, 1, 10);


        return View::make(
            'pages.loan',
            ['loan' => $loan, 'borrower' => $borrower, 'loan_id' => $loanId, 'comments' => $comments]
        );
    }
}