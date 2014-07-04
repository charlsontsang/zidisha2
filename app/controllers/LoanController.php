<?php

use SupremeNewMedia\Finance\Core\Currency;
use SupremeNewMedia\Finance\Core\Money;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Comment\CommentService;
use Zidisha\Flash\Flash;
use Zidisha\Lender\Exceptions\InsufficientLenderBalanceException;
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
        \Zidisha\Payment\Form\PlaceBidForm $bidForm,
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

        $stillNeeded = $loan->getAmount()->getAmount() - $totalRaised;

        if($loan->getAmount() <= $totalRaised){
            $raised = 100;
        }else{
            $raised = intval(($totalRaised/($loan->getAmount()->getAmount()))*100);
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
           return $form->makePayment();
        }

        Flash::error("Entered Amounts are invalid!");
        return Redirect::route('loan:index',$data['loanId'])->withForm($form);
    }

    public function postEditBid()
    {
        $data = \Input::all();

        $lender = \Auth::user()->getLender();

        $loan = LoanQuery::create()
            ->filterById($data['loanId'])
            ->findOne();

        $totalLoanAmount = $loan->getAmount();

        $amount =  Money::valueOf($data['amount'], Currency::valueOf('USD'));

        $oldBid = BidQuery::create()
            ->filterByLender($lender)
            ->filterByLoan($loan)
            ->findOne();

        if($oldBid->getBidAmount()->compare($amount) != -1){
            \Flash::error(\Lang::get('Loan.edit-bid.amount'));
            return Redirect::back()->withInput();
        }

        if($totalLoanAmount->getAmount() <= $data['amount']){
            \Flash::error(\Lang::get('Loan.edit-bid.interest-rate'));
            return Redirect::back()->withInput();
        }

        if($data['interestRate'] <= $oldBid->getInterestRate()){
            \Flash::error(\Lang::get('Loan.edit-bid.bid-amount-exceded'));
            return Redirect::back()->withInput();
        }

        $bid = BidQuery::create()
            ->filterByLoan($loan)
            ->filterByLender($lender)
            ->findOne();

        $this->loanService->editBid($bid, $data);

        Flash::success(\Lang::get('Loan.edit-bid.success') . $data['amount']);
        return Redirect::route('loan:index', $data['loanId']);
    }
}
