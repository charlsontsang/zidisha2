<?php

use Illuminate\Support\Facades\View;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Admin\Setting;
use Zidisha\Balance\InviteTransactionQuery;
use Zidisha\Balance\Transaction;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Comment\BorrowerCommentService;
use Zidisha\Lender\Lender;
use Zidisha\Loan\Paginator\ActiveLoanBids;
use Zidisha\Loan\Paginator\FundraisingLoanBids;
use Zidisha\Currency\Currency;
use Zidisha\Currency\Money;
use Zidisha\Lender\Form\EditProfile;
use Zidisha\Lender\Form\Funds;
use Zidisha\Lender\Form\GiftCard;
use Zidisha\Lender\Form\WithdrawFundsForm;
use Zidisha\Lender\GiftCardQuery;
use Zidisha\Lender\InviteQuery;
use Zidisha\Lender\LenderQuery;
use Zidisha\Lender\LenderService;
use Zidisha\Lender\LendingGroupQuery;
use Zidisha\Loan\Bid;
use Zidisha\Loan\BidQuery;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Payment\BalanceService;
use Zidisha\Payment\Form\UploadFundForm;
use Zidisha\Payment\Stripe\StripeService;
use Zidisha\Repayment\RepaymentService;
use Zidisha\Utility\Utility;

class LenderController extends BaseController
{
    protected $transactionQuery;

    private $fundsForm, $cardForm;

    private $lenderService;
    private $uploadFundForm;
    private $withdrawFundsForm;
    private $repaymentService;
    private $balanceService;
    private $borrowerCommentService;


    public function __construct(
        TransactionQuery $transactionQuery,
        Funds $fundsForm,
        LenderService $lenderService,
        GiftCard $cardForm,
        UploadFundForm $uploadFundForm,
        WithdrawFundsForm $withdrawFundsForm,
        RepaymentService $repaymentService,
        BalanceService $balanceService,
        BorrowerCommentService $borrowerCommentService
    ) {
        $this->transactionQuery = $transactionQuery;
        $this->fundsForm = $fundsForm;
        $this->lenderService = $lenderService;
        $this->cardForm = $cardForm;
        $this->uploadFundForm = $uploadFundForm;
        $this->withdrawFundsForm = $withdrawFundsForm;
        $this->repaymentService = $repaymentService;
        $this->balanceService = $balanceService;
        $this->borrowerCommentService = $borrowerCommentService;
    }

    public function getPublicProfile($username)
    {
        $lender = LenderQuery::create()
            ->useUserQuery()
            ->filterByUsername($username)
            ->endUse()
            ->findOne();
        if (!$lender) {
            \App::abort(404);
        }

        $karma = $this->lenderService->getKarma($lender);

        return View::make(
            'lender.public-profile',
            compact('lender', 'karma')
        );
    }

    public function getWelcome()
    {        
        return View::make('lender.welcome');        
    }
    
    public function getEditProfile()
    {
        $lender = \Auth::user()->getLender();
        if (!$lender) {
            \App::abort(404);
        }

        $form = new EditProfile($lender);

        return View::make(
            'lender.edit-profile',
            compact('form')
        );
    }

    public function postEditProfile()
    {
        $lender = \Auth::user()->getLender();
        if (!$lender) {
            \App::abort(404);
        }

        $form = new EditProfile($lender);
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();

            $lender = Auth::user()->getLender();

            $this->lenderService->editProfile($lender, $data);

            if (Input::hasFile('picture')) {
                $image = Input::file('picture');
                $this->lenderService->uploadPicture($lender, $image);
            }

            return Redirect::route('lender:public-profile', $data['username']);
        }

        return Redirect::route('lender:edit-profile')->withForm($form);
    }

    public function getDashboard()
    {
        /** @var Lender $lender */
        $lender = \Auth::user()->getLender();
        $userId = \Auth::user()->getId();
        if (!$lender) {
            \App::abort(404);
        }

        $totalFundsUpload = TransactionQuery::create()
            ->getTotalFundsUpload($userId);

        $currentBalance = TransactionQuery::create()
            ->getCurrentBalance($userId);

        $newMemberInviteCredit = InviteTransactionQuery::create()
            ->getTotalInviteCreditAmount($lender->getId());

        $lendingGroups = LendingGroupQuery::create()
            ->getLendingGroupsForLender($lender);

        $numberOfInvitesSent = InviteQuery::create()
            ->filterByLender($lender)
            ->count();
        $AcceptedInviteesIds = InviteQuery::create()
            ->getAcceptedInviteesIds($lender);
        $numberOfInvitesAccepted = $AcceptedInviteesIds->count();

        $numberOfGiftedGiftCards = GiftCardQuery::create()
            ->filterByLender($lender)
            ->count();

        $RedeemedGiftCardsRecipientsIds = GiftCardQuery::create()
            ->getRedeemedGiftCardsRecipientsIds($lender);
        $numberOfRedeemedGiftCards = $RedeemedGiftCardsRecipientsIds->count();

        $totalLentAmount = TransactionQuery::create()
            ->getTotalLentAmount($userId);

        $myImpact = $this->lenderService->getMyImpact($lender);
        $totalLentAmountByInvitees = $this->lenderService->getTotalAmountLentByInvitee($lender);
        $totalLentAmountByRecipients = $myImpact->subtract($totalLentAmountByInvitees);
        $totalImpact = $myImpact->add($totalLentAmount);

        $comments = $this->borrowerCommentService->getAllCommentForLender($lender);

       return View::make('lender.dashboard', compact('currentBalance', 'totalFundsUpload', 'lendingGroups',
                'numberOfInvitesSent', 'numberOfInvitesAccepted', 'numberOfGiftedGiftCards', 'numberOfRedeemedGiftCards', 
                'totalLentAmount', 'totalImpact' , 'loans', 'newMemberInviteCredit',
                'totalLentAmountByInvitees', 'totalLentAmountByRecipients',
                'comments'
            ));
    }

    public function getTransactionHistory()
    {
        if (Auth::check() && Auth::user()->isAdmin() && !Request::query('lenderId')) {
            \App::abort(404, 'Please enter a proper lenderId');
        }
        
        if (Auth::check() && Auth::user()->isAdmin() && Request::query('lenderId')) {
            $userId = Request::query('lenderId');
            
            $this->getLenderById($userId);
            
        } else {
            $userId = Auth::getUser()->getId();
        }

        $currentBalance = TransactionQuery::create()
            ->getCurrentBalance($userId);

        $page = Request::query('page') ? : 1;

        $currentBalancePageObj = DB::select(
            'SELECT SUM(amount) AS total
             FROM transactions
             WHERE id IN (SELECT id
                          FROM transactions WHERE user_id = ?
                          ORDER BY transaction_date DESC, transactions.id DESC
                          OFFSET ?)',
            array($userId, ($page - 1) * 50)
        );

        $currentBalancePage = Money::create($currentBalancePageObj[0]->total);

        $paginator = $this->transactionQuery->create()
            ->orderByTransactionDate('desc')
            ->orderById('desc')
            ->filterByUserId($userId)
            ->paginate($page, 50);

        return View::make('lender.history', compact('paginator', 'currentBalance', 'currentBalancePage'));
    }

    public function getFunds()
    {
        $currentBalance = TransactionQuery::create()
            ->getCurrentBalance(Auth::getUser()->getId());

        return View::make('lender.funds', compact('currentBalance'), ['form' => $this->uploadFundForm,]);
    }

    public function postFunds()
    {
        $form = $this->uploadFundForm;
        $form->handleRequest(\Request::instance());

        if ($form->isValid()) {
            $country = Utility::getCountryCodeByIP();
            $blockedCountries = Setting::get('site.countriesCodesBlockedFromUploadFunds');
            $blockedCountries = explode(',', $blockedCountries);

            if (in_array($country['code'], $blockedCountries)) {
                \Flash::error('common.validation.error');
                return Redirect::route('lender:funds')->withForm($form);
            }

            return $form->makePayment();
        }

        \Flash::error("Please enter the amount as a number.");
        return Redirect::route('lender:funds')->withForm($form);
    }

    public function postWithdrawalFund()
    {
        $form = $this->withdrawFundsForm;
        $form->handleRequest(\Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();
            $lender = Auth::user()->getLender();
            $withdrawalRequest = $this->balanceService->addWithdrawRequest($lender, $data);
            if ($withdrawalRequest) {
                \Flash::success("Your withdrawal request has been submitted, and should be processed within one week. Thanks for your participation!");
                return Redirect::route('lender:funds');
            }
        }

        \Flash::error("Please enter the amount as a number.");
        return Redirect::route('lender:funds')->withForm($form);
    }

    public function getLoans()
    {
        if (Auth::check() && Auth::user()->isAdmin() && !Request::query('lenderId')) {
            \App::abort(404, 'Please enter a proper lenderId');
        }

        if (Auth::check() && Auth::user()->isAdmin() && Request::query('lenderId')) {
            $userId = Request::query('lenderId');
            $lender = $this->getLenderById($userId);
        } else {
            $userId = Auth::getUser()->getId();
            $lender = \Auth::user()->getLender();
        }
        
        if (!$lender) {
            \App::abort(404);
        }

        $page = Request::query('page') ? : 1;
        $page2 = Request::query('page2') ? : 1;
        $page3 = Request::query('page3') ? : 1;
        
        $activeLoanBidPaymentStatus = [];
        $completedLoansBidAmountRepaid = [];
        $netChangeCompletedBid = [];
        $totalNetChangeCompletedBid = Money::create(0, 'USD');
        $activeLoanBidAmountRepaid = [];
        $activeLoanBidPrincipleOutstanding = [];

        $totalFundsUpload = TransactionQuery::create()
            ->getTotalFundsUpload($userId);

        $numberOfLoans = LoanQuery::create()
            ->getNumberOfLoansForLender($lender);

        $currentBalance = TransactionQuery::create()
            ->getCurrentBalance($userId);

        $lenderInviteCredit = InviteTransactionQuery::create()
            ->getTotalInviteCreditAmount($lender->getId());

        $principleOutstanding = BidQuery::create()
            ->getTotalOutstandingAmount($lender);

        $totalLentAmount = TransactionQuery::create()
            ->getTotalLentAmount($userId);

        $myImpact = $this->lenderService->getMyImpact($lender);
        $totalImpact = $myImpact->add($totalLentAmount);

        $fundraisingLoanBids = new FundRaisingLoanBids($lender, $page);
        $activeLoanBids = new ActiveLoanBids($lender, $page2);

        $completedLoansBids = BidQuery::create()
            ->getCompletedLoansBids($lender, $page3);
        $totalCompletedLoansBidsAmount = BidQuery::create()
            ->getTotalCompletedLoansBidsAmount($lender);
        $numberOfCompletedBids = $completedLoansBids->getNbResults();
        $numberOfCompletedProjects = \Lang::choice('lender.shared-labels.projects.stats-projects', $numberOfCompletedBids, array('count' => $numberOfCompletedBids));

        $completedLoansIds = [];
        /** @var $completedLoansBid Bid */
        foreach ($completedLoansBids as $completedLoansBid) {
            $completedLoansIds[] = $completedLoansBid->getLoanId();
        }

        $completedLoansRepaidAmounts = TransactionQuery::create()
            ->getCompletedLoansRepaidAmounts($userId, $completedLoansIds);

        $totalCompletedLoansRepaidAmount = TransactionQuery::create()
            ->getTotalCompletedLoansRepaidAmount($userId);

        /** @var $completedLoansBid Bid */
        foreach ($completedLoansBids as $completedLoansBid) {
            foreach ($completedLoansRepaidAmounts as $completedLoansRepaidAmount) {
                if ($completedLoansRepaidAmount['loan_id'] == $completedLoansBid->getLoanId()) {
                    $completedLoansBidAmountRepaid[$completedLoansBid->getId()] = Money::create($completedLoansRepaidAmount['totals'], 'USD');
                    continue;
                }
                $completedLoansBidAmountRepaid[$completedLoansBid->getId()] = Money::create(0, 'USD');
            }
            $netChangeCompletedBid[$completedLoansBid->getId()] = $completedLoansBidAmountRepaid[$completedLoansBid->getId()]->subtract($completedLoansBid->getAcceptedAmount());
            $totalNetChangeCompletedBid= $totalNetChangeCompletedBid->add($netChangeCompletedBid[$completedLoansBid->getId()]);
        }

       return View::make('lender.loans', compact(
           'currentBalance',
           'totalFundsUpload',
           'numberOfLoans',
           'totalLentAmount',
           'myImpact',
           'totalImpact',
           'loans',
           'fundraisingLoanBids',
           'activeLoanBids',
           'completedLoansBids',
           'totalCompletedLoansBidsAmount',
           'numberOfFundRaisingProjects',
           'lenderInviteCredit',
           'numberOfCompletedProjects',
           'principleOutstanding',
           'completedLoansBidAmountRepaid',
           'totalCompletedLoansRepaidAmount',
           'netChangeCompletedBid',
           'totalNetChangeCompletedBid'
        ));
    }

    /**
     * @param $userId
     * @return \Zidisha\Lender\Lender
     */
    protected function getLenderById($userId)
    {
        $lender = LenderQuery::create()
            ->findOneById($userId);

        if (!$lender) {
            \App::abort(404, 'Invalid Lender id');
        }
        
        return $lender;
    }
}
