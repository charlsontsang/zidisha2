<?php

use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Comment\CommentService;
use Zidisha\Loan\Bid;
use Zidisha\Loan\Form\BidForm;
use Zidisha\Loan\Loan;
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
    protected $bidForm;

    public function  __construct(
        LoanQuery $loanQuery,
        CommentService $commentService,
        BidQuery $bidQuery,
        BidForm $bidForm
    ) {
        $this->loanQuery = $loanQuery;
        $this->bidQuery = $bidQuery;
        $this->commentService = $commentService;
        $this->bidForm = $bidForm;
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

            $loan = $this->loanQuery->create()
                ->filterById($data['loanId'])
                ->findOne();

            $oneBid = new Bid();
            $oneBid->setBidDate(new \DateTime());
            $oneBid->setBidAmount($data['Amount']);
            $oneBid->setInterestRate($data['interestRate']);
            $oneBid->setLoan($loan);
            $oneBid->setLender(Auth::user()->getLender());
            $oneBid->setBorrower($loan->getBorrower());
            $oneBid->save();

            Flash::success("Successful bid of amount USD " . $data['Amount']);
            return Redirect::route('loan:index', $data['loanId']);
        }

        Flash::error("Entered Amounts are invalid!");
        return Redirect::route('loan:index',$data['loanId'])->withForm($form);
    }
}