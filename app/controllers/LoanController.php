<?php

use SupremeNewMedia\Finance\Core\Currency;
use SupremeNewMedia\Finance\Core\Money;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\BorrowerService;
use Zidisha\Comment\BorrowerCommentService;
use Zidisha\Comment\CommentService;
use Zidisha\Flash\Flash;
use Zidisha\Lender\Exceptions\InsufficientLenderBalanceException;
use Zidisha\Loan\Bid;
use Zidisha\Loan\Form\AdminCategoryForm;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\BidQuery;
use Zidisha\Loan\LoanService;
use Zidisha\Payment\Form\EditBidForm;
use Zidisha\Payment\Form\PlaceBidForm;

class LoanController extends BaseController
{

    protected $loanQuery;

    /**
     * @var Zidisha\Loan\BidQuery
     */
    protected $bidQuery;
    private $loanService;
    private $borrowerService;
    private $adminCategoryForm;

    public function  __construct(
        LoanQuery $loanQuery,
        BidQuery $bidQuery,
        LoanService $loanService,
        BorrowerService $borrowerService,
        AdminCategoryForm $adminCategoryForm,
        BorrowerCommentService $borrowerCommentService
    ) {
        $this->loanQuery = $loanQuery;
        $this->bidQuery = $bidQuery;
        $this->loanService = $loanService;
        $this->borrowerService = $borrowerService;
        $this->adminCategoryForm = $adminCategoryForm;
        $this->borrowerCommentService = $borrowerCommentService;
    }

    public function getIndex($loanId)
    {
        //for loan
        $loan = $this->loanQuery
            ->filterById($loanId)
            ->findOne();
        
        if (!$loan) {
            App::abort(404);
        }

        $page = 1;
        if (Input::has('page')) {
            $page = Input::get('page');
        }

        $borrower = $receiver = $loan->getBorrower();
        $comments = $this->borrowerCommentService->getPaginatedComments($borrower, $page, 10);

        $bids = $this->bidQuery->create()
            ->filterByLoan($loan)
            ->orderByBidDate()
            ->find();

        $calculator = new \Zidisha\Loan\Calculator\InstallmentCalculator($loan);
        $totalInterest = $calculator->totalInterest();
        $serviceFee = $calculator->serviceFee();
        $previousLoans = $this->borrowerService->getPreviousLoans($borrower, $loan);

        $placeBidForm = new PlaceBidForm($loan);

        return View::make(
            'pages.loan',
            compact(
                'loan', 'borrower', 'bids', 'comments',
                'totalInterest', 'serviceFee', 'previousLoans'),
            ['placeBidForm' => $placeBidForm, 'categoryForm' =>$this->adminCategoryForm]
        );
    }

    public function postPlaceBid($loanId)
    {
        $loan = $this->loanQuery
            ->filterById($loanId)
            ->findOne();

        if (!$loan) {
            App::abort(404);
        }
        
        $form = new PlaceBidForm($loan);
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
           return $form->makePayment();
        }

        Flash::error("Entered Amounts are invalid!");
        return Redirect::route('loan:index',$loanId)->withForm($form);
    }

    public function postEditBid($loanId, $bidId)
    {
        $data = \Input::all();

        $lender = \Auth::user()->getLender();

        $bid = BidQuery::create()
            ->filterById($bidId)
            ->filterByLoanId($loanId)
            ->filterByLender($lender)
            ->findOne();

        if (!$bid) {
            App::abort(404);
        }
        
        $editBidForm = new EditBidForm($bid);
        $editBidForm->handleRequest(Request::instance());
            
        if (!$editBidForm->isValid()) {
            Flash::success(\Lang::get('Loan.edit-bid.failed') . $data['amount']);
            return Redirect::route('loan:index', $bid->getLoanId());
        }

        // TODO form->makePayment
        $bid = $this->loanService->editBid($bid, $data);

        Flash::success(\Lang::get('Loan.edit-bid.success') . $bid->getBidAmount());
        return Redirect::route('loan:index', $bid->getLoanId());
    }
}
