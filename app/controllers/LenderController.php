<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Propel\Runtime\Propel;
use Zidisha\Balance\Map\TransactionTableMap;
use Zidisha\Balance\Transaction;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Currency\Money;
use Zidisha\Lender\Form\EditProfile;
use Zidisha\Lender\Form\Funds;
use Zidisha\Lender\LenderQuery;
use Zidisha\Lender\LenderService;
use Zidisha\Lender\ProfileQuery;
use Zidisha\Payment\Payment;
use Zidisha\Payment\Stripe\StripeService;
use Zidisha\Utility\Utility;

class LenderController extends BaseController
{
    protected $transactionQuery;

    /**
     * @var Zidisha\Lender\Form\EditProfile
     */
    private $editProfileForm, $fundsForm;
    /**
     * @var Zidisha\Lender\LenderService
     */
    private $lenderService;
    /**
     * @var StripeService
     */
    private $stripeService;

    public function __construct(EditProfile $editProfileForm, TransactionQuery $transactionQuery, Funds $fundsForm, LenderService $lenderService, StripeService $stripeService)
    {
        $this->editProfileForm = $editProfileForm;
        $this->transactionQuery = $transactionQuery;
        $this->fundsForm = $fundsForm;
        $this->lenderService = $lenderService;
        $this->stripeService = $stripeService;
    }

    public function getPublicProfile($username)
    {
        $lender = LenderQuery::create()
            ->useUserQuery()
            ->filterByUsername($username)
            ->endUse()
            ->findOne();

        if(!$lender){
            \Illuminate\Support\Facades\App::abort(404);
        }
        return View::make(
            'lender.public-profile',
            compact('lender')
        );
    }

    public function getEditProfile()
    {
        return View::make(
            'lender.edit-profile',
            ['form' => $this->editProfileForm,]
        );
    }

    public function postEditProfile()
    {
        $form = $this->editProfileForm;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();

            $lender = Auth::user()->getLender();

            $this->lenderService->editProfile($lender, $data);

            if(Input::hasFile('picture'))
            {
                $image = Input::file('picture');
                $this->lenderService->uploadPicture($lender, $image);
            }

            return Redirect::route('lender:public-profile', $data['username']);
        }

        return Redirect::route('lender:edit-profile')->withForm($form);
    }

    public function getDashboard(){
        return View::make('lender.dashboard');
    }

    public function getTransactionHistory(){

        $currentBalance = $this->transactionQuery
            ->filterByUserId(Auth::getUser()->getId())
            ->getTotalBalance();

        $page = Request::query('page') ?: 1;

        $currentBalancePageObj = DB::select(
            'SELECT SUM(amount) AS total
             FROM transactions
             WHERE id IN (SELECT id
                          FROM transactions WHERE user_id = ?
                          ORDER BY transaction_date DESC, transactions.id DESC
                          OFFSET ?)',
            array(Auth::getUser()->getId(), ($page-1) * 50));

        $currentBalancePage = $currentBalancePageObj[0]->total;

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

        return View::make('lender.funds', compact('currentBalance'), ['form' => $this->fundsForm,]);
    }

    public function postFunds()
    {
        $form = $this->fundsForm;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();
            $country = Utility::getCountryCodeByIP();
            $blockedCountries = \Config::get('blockedCountries.codes');
            if(in_array($country['code'],$blockedCountries )){
                Flash::error("Something went wrong!");
                return Redirect::route('lender:funds')->withForm($form);
            }

            $payment = new Payment();
            $payment
                ->setAmount(Money::create($data['creditAmount']))
                ->setTransactionFee(Money::create($data['feeAmount']))
                ->setTotalAmount(Money::create($data['totalAmount']))
                ->setDonationAmount(Money::create($data['totalAmount']));
            $payment->save();


            return $this->stripeService->makePayment($payment, ['stripeToken' => $data['stripeToken']]);
        }

        Flash::error("Entered Amounts are invalid!");
        return Redirect::route('lender:funds')->withForm($form);
    }
}
