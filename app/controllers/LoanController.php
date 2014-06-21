<?php

use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Comment\CommentService;
use Zidisha\Loan\Bid;
use Zidisha\Loan\Form\BidForm;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\BidQuery;
use Zidisha\Loan\LoanService;

class LoanController extends BaseController
{

    protected $loanQuery;
    /**
     * @var Zidisha\Comment\CommentService
     */
    private $commentService;

    /**
     * @var Zidisha\Loan\BidQuery
     */
    protected $bidQuery;

    /**
     * @var Zidisha\Loan\Form\BidForm
     */
    protected $bidForm;
    /**
     * @var Zidisha\Loan\LoanService
     */
    private $loanService;

    public function  __construct(
        LoanQuery $loanQuery,
        CommentService $commentService,
        BidQuery $bidQuery,
        BidForm $bidForm,
        LoanService $loanService
    ) {
        $this->loanQuery = $loanQuery;
        $this->bidQuery = $bidQuery;
        $this->commentService = $commentService;
        $this->bidForm = $bidForm;
        $this->loanService = $loanService;
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
        if($loan->getAmount() <= $totalRaised){
            $raised = 100;
        }else{
            $raised = intval(($totalRaised/($loan->getAmount()))*100);
        }

        return View::make(
            'pages.loan',
            compact('loan', 'borrower' , 'bids', 'totalRaised', 'stillNeeded', 'comments', 'raised'),
            ['form' => $this->bidForm,]
        );
    }

    public function postBid()
    {
        $form = $this->bidForm;
        $form->handleRequest(Request::instance());
        $data = $form->getData();

        if ($form->isValid()) {

            $loan = LoanQuery::create()
                ->filterById($data['loanId'])
                ->findOne();

            $lender = \Auth::user()->getLender();

            $this->loanService->placeBid($loan, $lender, $data);

            Flash::success("Successful bid of amount USD " . $data['Amount']);
            return Redirect::route('loan:index', $data['loanId']);
        }

        Flash::error("Entered Amounts are invalid!");
        return Redirect::route('loan:index',$data['loanId'])->withForm($form);
    }
}