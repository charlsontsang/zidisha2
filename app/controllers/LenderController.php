<?php

use Illuminate\Support\Facades\View;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Admin\Setting;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Currency\Currency;
use Zidisha\Currency\Money;
use Zidisha\Lender\Form\EditProfile;
use Zidisha\Lender\Form\Funds;
use Zidisha\Lender\Form\GiftCard;
use Zidisha\Lender\Form\WithdrawFundsForm;
use Zidisha\Lender\LenderQuery;
use Zidisha\Lender\LenderService;
use Zidisha\Loan\BidQuery;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Payment\Form\UploadFundForm;
use Zidisha\Payment\Stripe\StripeService;
use Zidisha\Utility\Utility;

class LenderController extends BaseController
{
    protected $transactionQuery;

    private $fundsForm, $cardForm;

    private $lenderService;
    private $uploadFundForm;
    private $withdrawFundsForm;


    public function __construct(
        TransactionQuery $transactionQuery,
        Funds $fundsForm,
        LenderService $lenderService,
        GiftCard $cardForm,
        UploadFundForm $uploadFundForm,
        WithdrawFundsForm $withdrawFundsForm

    ) {
        $this->transactionQuery = $transactionQuery;
        $this->fundsForm = $fundsForm;
        $this->lenderService = $lenderService;
        $this->cardForm = $cardForm;
        $this->uploadFundForm = $uploadFundForm;
        $this->withdrawFundsForm = $withdrawFundsForm;
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
            ->filterByLender($lender)
            ->filterByActive(true)
            ->paginate($page , 10);
        $totalBidAmount = BidQuery::create()
            ->getTotalActiveBidAmount($lender);

        $activeLoansBids = BidQuery::create()
            ->filterByLender($lender)
            ->filterByAcceptedAmount('0', Criteria::NOT_EQUAL)
            ->useLoanQuery()
                ->filterActive()
            ->endUse()
            ->paginate($page2, 10);

        $total = BidQuery::create()
            ->filterByLender($lender)
            ->filterByAcceptedAmount('0', Criteria::NOT_EQUAL)
            ->useLoanQuery()
                ->filterActive()
            ->endUse()
            ->select(array('total'))
            ->withColumn('SUM(accepted_amount)', 'total')
            ->findOne();
        $totalActiveLoansBidsAmount =  Money::valueOf($total, Currency::valueOf('USD'));


        $completedLoansBids = BidQuery::create()
            ->filterByLender($lender)
            ->filterByAcceptedAmount('0', Criteria::NOT_EQUAL)
            ->useLoanQuery()
                ->filterEnded()
            ->endUse()
            ->paginate($page3, 10);
        $total = BidQuery::create()
            ->filterByLender($lender)
            ->filterByAcceptedAmount('0', Criteria::NOT_EQUAL)
            ->useLoanQuery()
                ->filterEnded()
            ->endUse()
            ->select(array('total'))
            ->withColumn('SUM(accepted_amount)', 'total')
            ->findOne();
        $totalCompletedLoansBidsAmount =  Money::valueOf($total, Currency::valueOf('USD'));

        return View::make(
            'lender.public-profile',
            compact('lender', 'karma', 'activeBids', 'totalBidAmount', 'activeLoansBids', 'totalActiveLoansBidsAmount',
                    'completedLoansBids', 'totalCompletedLoansBidsAmount'
            )
        );
    }

    public function getEditProfile()
    {
        $lender = \Auth::user()->getLender();

        $form = new EditProfile($lender);

        return View::make(
            'lender.edit-profile',
            compact('form')
        );
    }

    public function postEditProfile()
    {
        $lender = \Auth::user()->getLender();

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
            ->select(array('total'))
            ->withColumn('SUM(amount)', 'total')
            ->filterByUserId(Auth::getUser()->getId())
            ->findOne();

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
        return View::make('lender.my-stats', compact('paginator', 'currentBalance', 'currentBalancePage'));
    }
}


