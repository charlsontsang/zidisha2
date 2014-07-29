<?php

use Illuminate\Support\Facades\View;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Admin\Setting;
use Zidisha\Balance\InviteTransactionQuery;
use Zidisha\Balance\Transaction;
use Zidisha\Balance\TransactionQuery;
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


    public function __construct(
        TransactionQuery $transactionQuery,
        Funds $fundsForm,
        LenderService $lenderService,
        GiftCard $cardForm,
        UploadFundForm $uploadFundForm,
        WithdrawFundsForm $withdrawFundsForm,
        RepaymentService $repaymentService
    ) {
        $this->transactionQuery = $transactionQuery;
        $this->fundsForm = $fundsForm;
        $this->lenderService = $lenderService;
        $this->cardForm = $cardForm;
        $this->uploadFundForm = $uploadFundForm;
        $this->withdrawFundsForm = $withdrawFundsForm;
        $this->repaymentService = $repaymentService;
    }

    public function getPublicProfile($username)
    {
        $lender = LenderQuery::create()
            ->useUserQuery()
            ->filterByUsername($username)
            ->endUse()
            ->findOne();
        if (!$lender) {
            \Illuminate\Support\Facades\App::abort(404);
        }

        $karma = $this->lenderService->getKarma($lender);
        $page = Request::query('page') ? : 1;
        $page2 = Request::query('page2') ? : 1;
        $page3 = Request::query('page3') ? : 1;

        $activeBids = BidQuery::create()
            ->getActiveBids($lender, $page);
        $totalBidAmount = BidQuery::create()
            ->getTotalActiveBidAmount($lender);

        $activeLoansBids = BidQuery::create()
            ->getActiveLoansBids($lender, $page2);
        $totalActiveLoansBidsAmount = BidQuery::create()
            ->getTotalActiveLoansBidsAmount($lender);

        $completedLoansBids = BidQuery::create()
            ->getCompletedLoansBids($lender, $page3);
        $totalCompletedLoansBidsAmount = BidQuery::create()
            ->getTotalCompletedLoansBidsAmount($lender);

        return View::make(
            'lender.public-profile',
            compact('lender', 'karma', 'activeBids', 'totalBidAmount',
                'activeLoansBids', 'totalActiveLoansBidsAmount', 'completedLoansBids', 'totalCompletedLoansBidsAmount')
        );
    }

    public function getEditProfile()
    {
        $lender = \Auth::user()->getLender();
        if (!$lender) {
            \Illuminate\Support\Facades\App::abort(404);
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
            \Illuminate\Support\Facades\App::abort(404);
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
        return View::make('lender.dashboard');
    }

    public function getTransactionHistory()
    {

        $currentBalance = $this->transactionQuery
            ->filterByUserId(Auth::getUser()->getId())
            ->getTotalAmount();

        $page = Request::query('page') ? : 1;

        $currentBalancePageObj = DB::select(
            'SELECT SUM(amount) AS total
             FROM transactions
             WHERE id IN (SELECT id
                          FROM transactions WHERE user_id = ?
                          ORDER BY transaction_date DESC, transactions.id DESC
                          OFFSET ?)',
            array(Auth::getUser()->getId(), ($page - 1) * 50)
        );

        $currentBalancePage = Money::create($currentBalancePageObj[0]->total);

        $paginator = $this->transactionQuery->create()
            ->orderByTransactionDate('desc')
            ->orderById('desc')
            ->filterByUserId(Auth::getUser()->getId())
            ->paginate($page, 50);

        return View::make('lender.history', compact('paginator', 'currentBalance', 'currentBalancePage'));
    }

    public function getFunds()
    {
        $currentBalance = $this->transactionQuery
            ->filterByUserId(Auth::getUser()->getId())
            ->getTotalAmount();

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
                \Flash::error("Something went wrong!");
                return Redirect::route('lender:funds')->withForm($form);
            }

            return $form->makePayment();
        }

        \Flash::error("Entered Amounts are invalid!");
        return Redirect::route('lender:funds')->withForm($form);
    }

    public function postWithdrawalFund()
    {
        $form = $this->withdrawFundsForm;
        $form->handleRequest(\Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();
            $lender = Auth::user()->getLender();
            $withdrawalRequest = $this->lenderService->addWithdrawRequest($lender, $data);
            if ($withdrawalRequest) {
                \Flash::success("Your withdrawal has been successfully processed, and the requested amount should be credited to your PayPal account within one week. Thanks for your participation!");
                return Redirect::route('lender:funds');
            }
        }

        \Flash::error("Entered Values are invalid!");
        return Redirect::route('lender:funds')->withForm($form);
    }

    public function getMyStats()
    {
        $lender = \Auth::user()->getLender();
        $userId = \Auth::user()->getId();
        if (!$lender) {
            \Illuminate\Support\Facades\App::abort(404);
        }
        $activeLoansBidPaymentStatus = [];
        $completedLoansBidAmountRepaid = [];
        $activeLoansBidAmountRepaid = [];
        $activeLoansBidPrincipleOutstanding = [];

        $totalFundsUpload = TransactionQuery::create()
            ->filterByUserId($userId)
            ->filterByType(Transaction::FUND_UPLOAD)
            ->getTotalAmount();
        $numberOfLoans = LoanQuery::create()
            ->useBidQuery()
                ->filterByLender($lender)
                ->filterByAcceptedAmount(null, Criteria::NOT_EQUAL)
            ->endUse()
            ->count();

        $currentBalance = $this->transactionQuery
            ->filterByUserId($userId)
            ->getTotalAmount();

        $newMemberInviteCredit = InviteTransactionQuery::create()
            ->getTotalInviteCreditAmount($lender);

        $totalOutstandingAmount = BidQuery::create()
            ->filterByActive(true)
            ->filterByLender($lender)
            ->useLoanQuery()
                ->filterByStatus([Loan::FUNDED, Loan::ACTIVE])
                ->filterNotForgivenByLender($lender)
            ->endUse()
            ->withColumn('SUM(accepted_amount * (100 - paid_percentage)/100)', 'total')
            ->select('total')
            ->findOne();

        $principleOutstanding = Money::create($totalOutstandingAmount, 'USD');

        $lendingGroups = LendingGroupQuery::create()
            ->useLendingGroupMemberQuery()
                ->filterByMember($lender)
                ->filterByLeaved(false)
            ->endUse()
            ->find();

        $numberOfInvitesSent = InviteQuery::create()
            ->filterByLender($lender)
            ->count();
        $AcceptedInviteesIds = InviteQuery::create()
            ->select('invitee_id')
            ->filterByLender($lender)
            ->filterByInviteeId(null, Criteria::NOT_EQUAL)
            ->find();
        $numberOfInvitesAccepted = $AcceptedInviteesIds->count();
        $numberOfLoansByInvitees = LoanQuery::create()
            ->useBidQuery()
                ->filterByLenderId($AcceptedInviteesIds, Criteria::IN)
                ->filterByAcceptedAmount(null, Criteria::NOT_EQUAL)
            ->endUse()
            ->count();

        $numberOfGiftedGiftCards = GiftCardQuery::create()
            ->filterByLender($lender)
            ->count();
        $RedeemedGiftCardsRecipientsIds = GiftCardQuery::create()
            ->select('recipient_id')
            ->filterByLender($lender)
            ->filterByRecipientId(null, Criteria::NOT_EQUAL)
            ->find();
        $numberOfRedeemedGiftCards = $RedeemedGiftCardsRecipientsIds->count();
        $numberOfLoansByRecipients = LoanQuery::create()
            ->useBidQuery()
            ->filterByLenderId($RedeemedGiftCardsRecipientsIds, Criteria::IN)
            ->filterByAcceptedAmount(null, Criteria::NOT_EQUAL)
            ->endUse()
            ->count();

        $totalLentAmount = TransactionQuery::create()
            ->getTotalLentAmount($userId);

        $myImpact = $this->lenderService->getMyImpact($lender);
        $totalLentAmountByInvitees = $this->lenderService->getTotalAmountLentByInvitee($lender);
        $totalLentAmountByRecipients = $myImpact->subtract($totalLentAmountByInvitees);
        $totalImpact = $myImpact->add($totalLentAmount);
        $page = Request::query('page') ? : 1;
        $page2 = Request::query('page2') ? : 1;
        $page3 = Request::query('page3') ? : 1;

        $activeBids = BidQuery::create()
            ->getActiveBids($lender, $page);
        $totalBidAmount = BidQuery::create()
            ->getTotalActiveBidAmount($lender);
        $numberOfFundRaisingBids = $activeBids->getNbResults();
        $numberOfFundRaisingProjects = \Lang::choice('lender.flash.preferences.stats-projects', $numberOfFundRaisingBids, array('count' => $numberOfFundRaisingBids));

        $activeLoansBids = BidQuery::create()
            ->getActiveLoansBids($lender, $page2);
        $totalActiveLoansBidsAmount = BidQuery::create()
            ->getTotalActiveLoansBidsAmount($lender);
        $numberOfActiveBids = $activeLoansBids->getNbResults();
        $numberOfActiveProjects = \Lang::choice('lender.flash.preferences.stats-projects', $numberOfActiveBids, array('count' => $numberOfActiveBids));

        $activeLoansIds = [];
        /** @var $activeLoansBid Bid */
        foreach ($activeLoansBids as $activeLoansBid) {
            $activeLoansIds[] = $activeLoansBid->getLoanId();
        }

        $activeLoansRepaidAmounts = TransactionQuery::create()
            ->filterByUserId($userId)
            ->filterRepaidToLender()
            ->select('totals', 'loan_id')
            ->withColumn('SUM(amount)', 'totals')
            ->withColumn('loan_id', 'loan_id')
            ->filterByLoanId($activeLoansIds, Criteria::IN)
            ->groupByLoanId()
            ->find();

        $ActiveLoansTotalOutstandingAmounts = BidQuery::create()
            ->filterByActive(true)
            ->filterByLender($lender)
            ->useLoanQuery()
                ->filterByStatus([Loan::FUNDED, Loan::ACTIVE])
                ->filterNotForgivenByLender($lender)
                ->filterById($activeLoansIds, Criteria::IN)
            ->endUse()
            ->select('total', 'loan_id')
            ->withColumn('SUM(accepted_amount * (100 - paid_percentage)/100)', 'total')
            ->withColumn('loan_id', 'loan_id')
            ->groupByLoanId()
            ->find();

        /** @var $activeLoansBid Bid */
        foreach ($activeLoansBids as $activeLoansBid) {
            foreach ($activeLoansRepaidAmounts as $activeLoansRepaidAmount) {
                if ($activeLoansRepaidAmount['loan_id'] == $activeLoansBid->getLoanId()) {
                    $activeLoansBidAmountRepaid[$activeLoansBid->getId()] = Money::create($activeLoansRepaidAmount['totals'], 'USD');
                    continue;
                }
                $activeLoansBidAmountRepaid[$activeLoansBid->getId()] = Money::create(0, 'USD');
            }
            foreach ($ActiveLoansTotalOutstandingAmounts as $ActiveLoansTotalOutstandingAmount) {
                if ($ActiveLoansTotalOutstandingAmount['loan_id'] == $activeLoansBid->getLoanId()) {
                    $activeLoansBidPrincipleOutstanding[$activeLoansBid->getId()] = Money::create($ActiveLoansTotalOutstandingAmount['total'], 'USD');
                    continue;
                }
                $activeLoansBidPrincipleOutstanding[$activeLoansBid->getId()] = Money::create(0, 'USD');
            }

            $repaymentSchedule = $this->repaymentService->getRepaymentSchedule($activeLoansBid->getLoan());
            $activeLoansBidPaymentStatus[$activeLoansBid->getId()] = $repaymentSchedule->getLoanPaymentStatus();
        }

        $completedLoansBids = BidQuery::create()
            ->getCompletedLoansBids($lender, $page3);
        $totalCompletedLoansBidsAmount = BidQuery::create()
            ->getTotalCompletedLoansBidsAmount($lender);
        $numberOfCompletedBids = $completedLoansBids->getNbResults();
        $numberOfCompletedProjects = \Lang::choice('lender.flash.preferences.stats-projects', $numberOfCompletedBids, array('count' => $numberOfCompletedBids));

        $completedLoansIds = [];
        /** @var $completedLoansBid Bid */
        foreach ($completedLoansBids as $completedLoansBid) {
            $completedLoansIds[] = $completedLoansBid->getLoanId();
        }

        $completedLoansRepaidAmounts = TransactionQuery::create()
            ->filterByUserId($userId)
            ->filterRepaidToLender()
            ->select('totals', 'loan_id')
            ->withColumn('SUM(amount)', 'totals')
            ->filterByLoanId($completedLoansIds, Criteria::IN)
            ->groupByLoanId()
            ->find();

        /** @var $completedLoansBid Bid */
        foreach ($completedLoansBids as $completedLoansBid) {
            $completedLoansBidAmountRepaid[$completedLoansBid->getId()] = $completedLoansRepaidAmounts[$completedLoansBid->getLoanId()];
        }

        return View::make('lender.my-stats', compact('currentBalance', 'totalFundsUpload', 'lendingGroups',
                'numberOfInvitesSent', 'numberOfInvitesAccepted', 'numberOfLoans', 'numberOfLoansByInvitees',
                'numberOfGiftedGiftCards', 'numberOfRedeemedGiftCards', 'numberOfLoansByRecipients',
                'totalLentAmount', 'myImpact', 'totalImpact' , 'loans', 'activeBids', 'totalBidAmount',
                'activeLoansBids', 'totalActiveLoansBidsAmount', 'completedLoansBids', 'totalCompletedLoansBidsAmount',
                'numberOfFundRaisingProjects', 'newMemberInviteCredit',
                'numberOfActiveProjects', 'numberOfCompletedProjects', 'principleOutstanding',
                'totalLentAmountByInvitees', 'totalLentAmountByRecipients',
                'activeLoansBidPaymentStatus', 'completedLoansBidAmountRepaid', 'activeLoansBidAmountRepaid',
                'activeLoansBidPrincipleOutstanding'
            ));
    }
}
