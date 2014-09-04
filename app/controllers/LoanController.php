<?php

use Zidisha\Borrower\BorrowerService;
use Zidisha\Comment\BorrowerCommentService;
use Zidisha\Comment\LoanFeedbackCommentService;
use Zidisha\Flash\Flash;
use Zidisha\Lender\FollowService;
use Zidisha\Loan\Form\AdminCategoryForm;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\BidQuery;
use Zidisha\Loan\LoanService;
use Zidisha\Payment\Form\EditBidForm;
use Zidisha\Payment\Form\PlaceBidForm;
use Zidisha\Repayment\RepaymentService;

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
    /**
     * @var Zidisha\Lender\FollowService
     */
    private $followService;
    private $repaymentService;
    /**
     * @var Zidisha\Comment\LoanFeedbackCommentService
     */
    private $loanFeedbackCommentService;

    public function  __construct(
        LoanQuery $loanQuery,
        BidQuery $bidQuery,
        LoanService $loanService,
        BorrowerService $borrowerService,
        AdminCategoryForm $adminCategoryForm,
        BorrowerCommentService $borrowerCommentService,
        FollowService $followService,
        RepaymentService $repaymentService,
        LoanFeedbackCommentService $loanFeedbackCommentService
    ) {
        $this->loanQuery = $loanQuery;
        $this->bidQuery = $bidQuery;
        $this->loanService = $loanService;
        $this->borrowerService = $borrowerService;
        $this->adminCategoryForm = $adminCategoryForm;
        $this->borrowerCommentService = $borrowerCommentService;
        $this->followService = $followService;
        $this->repaymentService = $repaymentService;
        $this->loanFeedbackCommentService = $loanFeedbackCommentService;
    }

    public function getIndex($loanId)
    {
        //for loan
        $loan = $this->loanQuery
            ->filterById($loanId)
            ->joinBorrower()
            ->findOne();

        if (!$loan) {
            App::abort(404);
        }

        //TODO:
        $displayFeedbackComments = $loan->isCompleted();

        $canPostFeedback = false;
        $canReplyFeedback = false;
        if ($displayFeedbackComments && Auth::check()) {
            $user = Auth::user();

            if ($user == $loan->getBorrower()->getUser()) {
                $canReplyFeedback = true;
            }

            $bidCount = BidQuery::create()
                ->filterByLoan($loan)
                ->filterByLenderId($user->getId())
                ->count();

            if ($bidCount) {
                $canPostFeedback = true;
                $canReplyFeedback = true;
            }
        }

        $page = Input::get('page', 1);

        $feedbackCommentPage = Input::get('feedbackPage', 1);

        $borrower = $receiver = $loan->getBorrower();
        $comments = $this->borrowerCommentService->getPaginatedComments($borrower, $page, 10);
        $commentCount = \Zidisha\Comment\BorrowerCommentQuery::create()
            ->filterByBorrower($borrower)
            ->filterByRemoved(false)
            ->count();
        $loanFeedbackComments = $this->loanFeedbackCommentService->getPaginatedComments($loan, $feedbackCommentPage, 10);

        $bids = $this->bidQuery->create()
            ->filterByLoan($loan)
            ->orderByBidAt()
            ->find();

        if ($loan->isDisbursed()) {
            $calculator = new \Zidisha\Loan\Calculator\InstallmentCalculator($loan);
            $totalInterest = $calculator->totalInterest();
            $serviceFee = $calculator->serviceFee();
        }
        $previousLoans = $this->borrowerService->getPreviousLoans($borrower, $loan);
        
        $invite = \Zidisha\Borrower\InviteQuery::create()
            ->filterByInviteeId($borrower->getId())
            ->joinBorrower()
            ->findOne();
        $invitedBy = $invite ? $invite->getBorrower() : null;
        
        $volunteerMentor = $borrower->getVolunteerMentor() ? $borrower->getVolunteerMentor()->getBorrowerVolunteer() : null;

        $placeBidForm = new PlaceBidForm($loan);

        $followersCount = $this->followService->getFollowerCount($borrower);
        $follower = false;
        if (\Auth::check() && \Auth::user()->isLender()) {
            $follower = $this->followService->getFollower(\Auth::user()->getLender(), $borrower);
        }
        $repaymentSchedule = $this->repaymentService->getRepaymentSchedule($loan);
        $repaymentScore = $this->loanService->getOnTimeRepaymentScore($borrower);

        if (strlen($loan->getSummary()) <= 60) {
            $tag = 'h2';
        } else {
            $tag = 'h3';
        }

        $categoryForm = $this->adminCategoryForm;
        
        return View::make(
            'pages.loan',
            compact(
                'bids',
                'loan',
                'follower',
                'borrower',
                'comments',
                'commentCount',
                'serviceFee',
                'previousLoans',
                'totalInterest',
                'followersCount',
                'canPostFeedback',
                'canReplyFeedback',
                'hasFundedBorrower',
                'repaymentSchedule',
                'repaymentScore',
                'loanFeedbackComments',
                'displayFeedbackComments',
                'tag',
                'placeBidForm',
                'categoryForm',
                'invitedBy',
                'volunteerMentor'
            )
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

        // TODO error message
        Flash::error("Something went wrong");
        return Redirect::route('loan:index', $loanId)->withForm($form);
    }

    public function getLoanSuccess($loanId)
    {
        $loan = $this->loanQuery
            ->filterById($loanId)
            ->findOne();

        if (!$loan) {
            App::abort(404);
        }

        $loanUrl = route('loan:index', $loanId);
        $name = $loan->getBorrower()->getFirstName();
        $country = $loan->getBorrower()->getCountry()->getName();

        $twitterParams = array(
            "url" => $loanUrl,
            "text" => "Just made a loan to $name in $country via @ZidishaInc",
        );
        $twitterUrl = "http://twitter.com/share?" . http_build_query($twitterParams);

        $relativeInviteUrl = str_replace("https://www.", "", $loanUrl);
        $relativeInviteUrl = str_replace("http://www.", "", $relativeInviteUrl);
        $facebookUrl = "http://www.facebook.com/sharer.php?s=100&p[url]=" . urlencode($relativeInviteUrl);
        $mailUrl = "mailto:?body=%0D%0A%0D%0A%0D%0A".$loanUrl;

        return View::make('pages.loan-success', compact('loan', 'twitterUrl', 'facebookUrl', 'mailUrl'));
    }
}
