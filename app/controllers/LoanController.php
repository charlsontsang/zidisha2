<?php

use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Borrower\BorrowerService;
use Zidisha\Comment\BorrowerCommentService;
use Zidisha\Comment\LoanFeedbackComment;
use Zidisha\Comment\LoanFeedbackCommentQuery;
use Zidisha\Comment\LoanFeedbackCommentService;
use Zidisha\Currency\Converter;
use Zidisha\Currency\Currency;
use Zidisha\Currency\CurrencyService;
use Zidisha\Flash\Flash;
use Zidisha\Lender\FollowService;
use Zidisha\Lender\LenderQuery;
use Zidisha\Loan\Calculator\InstallmentCalculator;
use Zidisha\Loan\Form\AdminCategoryForm;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\BidQuery;
use Zidisha\Loan\LoanService;
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
    
    /**
     * @var Zidisha\Currency\CurrencyService
     */
    private $currencyService;

    public function  __construct(
        LoanQuery $loanQuery,
        BidQuery $bidQuery,
        LoanService $loanService,
        BorrowerService $borrowerService,
        AdminCategoryForm $adminCategoryForm,
        BorrowerCommentService $borrowerCommentService,
        FollowService $followService,
        RepaymentService $repaymentService,
        LoanFeedbackCommentService $loanFeedbackCommentService,
        CurrencyService $currencyService
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
        $this->currencyService = $currencyService;
    }

    public function getIndex($loanId)
    {
        $loan = $this->loanQuery
            ->filterById($loanId)
            ->joinBorrower()
            ->findOne();

        if (!$loan) {
            App::abort(404);
        }

        $displayFeedbackComments = $loan->isCompleted();

        $canPostFeedback = false;
        $canReplyFeedback = false;
        if ($displayFeedbackComments && Auth::check()) {
            $user = $this->getUser();

            if ($user == $loan->getBorrower()->getUser()) {
                $canReplyFeedback = true;
            }

            $bidCount = BidQuery::create()
                ->filterByLoan($loan)
                ->filterByLenderId($user->getId())
                ->count();

            if ($bidCount) {
                $hasGivenFeedback = LoanFeedbackCommentQuery::create()
                    ->filterByUserId($user->getId())
                    ->filterByLoan($loan)
                    ->countFeedback($loan);
                
                if (!$hasGivenFeedback) {
                    $canPostFeedback = true;
                }
                $canReplyFeedback = true;
            }
        }

        $page = Input::get('page', 1);

        $feedbackCommentPage = Input::get('feedbackPage', 1);

        $borrower = $receiver = $loan->getBorrower();
        $comments = $this->borrowerCommentService->getPaginatedComments($borrower, $page, 50);
        $commentCount = \Zidisha\Comment\BorrowerCommentQuery::create()
            ->filterByBorrower($borrower)
            ->filterByRemoved(false)
            ->count();
        $loanFeedbackComments = $this->loanFeedbackCommentService->getPaginatedComments($loan, $feedbackCommentPage, 50);
        $loanFeedbackCounts = LoanFeedbackCommentQuery::create()
            ->getFeedbackRatingCounts($loan); 

        $lenders = LenderQuery::create()->findBidOnLoan($loan);

        if ($loan->isDisbursed()) {
            $calculator = new InstallmentCalculator($loan);
            $disbursedExchangeRate = $this->currencyService->getExchangeRate($loan->getCurrency(), $loan->getDisbursedAt()); 
            
            $disbursedAmount = Converter::toUSD($calculator->amount(), $disbursedExchangeRate);
            $lenderInterest = Converter::toUSD($calculator->lenderInterest(), $disbursedExchangeRate);
            $serviceFee = Converter::toUSD($calculator->serviceFee(), $disbursedExchangeRate);
            $totalAmount = Converter::toUSD($calculator->totalAmount(), $disbursedExchangeRate);
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

        $categoryForm = $this->adminCategoryForm;

        $loanIds = LoanQuery::create()
        ->getAllLoansForBorrower($borrower);

        $totalPositiveFeedback = LoanFeedbackCommentQuery::create()
            ->filterByLoanId($loanIds, Criteria::IN)
            ->filterByRating(LoanFeedbackComment::POSITIVE)
            ->countFeedback();

        $totalFeedback = LoanFeedbackCommentQuery::create()
            ->filterByLoanId($loanIds, Criteria::IN)
            ->countFeedback();
        
        if ($totalFeedback > 0) {
            $feedbackRating = round($totalPositiveFeedback * 100 / $totalFeedback);
        } else {
            $feedbackRating = 0;
        }

        return View::make(
            'pages.loan',
            compact(
                'lenders',
                'loan',
                'follower',
                'borrower',
                'comments',
                'commentCount',
                'disbursedExchangeRate',
                'disbursedAmount',
                'lenderInterest',
                'serviceFee',
                'totalAmount',
                'previousLoans',
                'followersCount',
                'canPostFeedback',
                'canReplyFeedback',
                'hasFundedBorrower',
                'repaymentSchedule',
                'repaymentScore',
                'loanFeedbackComments',
                'loanFeedbackCounts',
                'displayFeedbackComments',
                'placeBidForm',
                'categoryForm',
                'invitedBy',
                'volunteerMentor',
                'feedbackRating',
                'totalFeedback'
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
