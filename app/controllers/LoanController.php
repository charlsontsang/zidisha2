<?php

use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Comment\CommentService;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\BidQuery;

class LoanController extends BaseController
{

    protected $loanQuery;
    /**
     * @var Zidisha\Comment\CommentService
     */
    private $commentService;

    protected $bidQuery;

    public function  __construct(
        LoanQuery $loanQuery,
        CommentService $commentService,
        BidQuery $bidQuery
    ) {
        $this->loanQuery = $loanQuery;
        $this->bidQuery = $bidQuery;
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

        $totalRaised = $this->bidQuery
            ->select(array('total'))
            ->withColumn('SUM(bid_amount)', 'total')
            ->filterByLoan($loan)
            ->findOne();

        $bids = $this->bidQuery->create()
            ->filterByLoan($loan)
            ->orderByBidDate()
            ->find();

        $stillNeeded = $loan->getAmount() - $totalRaised;

        return View::make(
            'pages.loan',
            compact('loan', 'borrower' , 'bids', 'totalRaised', 'stillNeeded', 'comments')
        );
    }
}