<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Propel\Runtime\Propel;
use Zidisha\Balance\Map\TransactionTableMap;
use Zidisha\Balance\Transaction;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Lender\Form\EditProfile;
use Zidisha\Lender\Form\Funds;
use Zidisha\Lender\LenderQuery;
use Zidisha\Lender\LenderService;
use Zidisha\Lender\ProfileQuery;
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

    public function __construct(EditProfile $editProfileForm, TransactionQuery $transactionQuery, Funds $fundsForm, LenderService $lenderService)
    {
        $this->editProfileForm = $editProfileForm;
        $this->transactionQuery = $transactionQuery;
        $this->fundsForm = $fundsForm;
        $this->lenderService = $lenderService;
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
            ->select(array('total'))
            ->withColumn('SUM(amount)', 'total')
            ->filterByUserId(Auth::getUser()->getId())
            ->findOne();

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
            \Stripe::setApiKey(\Config::get('stripe.secret_key'));

            $payment_success = false;
            try {
                $email = Auth::getUser()->getEmail();
                $charge = \Stripe_Charge::create(array(
                        "amount" => $data['totalAmount'] * 100, // amount in cents, again
                        "currency" => "usd",
                        "card" => $data['stripeToken'],
                        "description" => $email)
                );
                $payment_success = true;
            } catch (Stripe_Error $e) {
                // TODO Flash Error
                Log::error("Stripe error: userid  " . Auth::getUser()->getId());
                Log::error("Stripe error: token   " . $data['stripeToken']);
                Log::error("Stripe error: message " . $e->getMessage());
                Log::error("Stripe error: status  " . $e->getHttpStatus());
                Log::error("Stripe error: body    " . $e->getHttpBody());
            }

            if ($payment_success) {
                $stripe_tran_fee= $data['feeAmount'] * -1;
                $con = Propel::getWriteConnection(TransactionTableMap::DATABASE_NAME);

                for ($retry = 0; $retry < 3; $retry++ ) {
                    $con->beginTransaction();

                    $transactionUpload = new Transaction();
                    $transactionUpload->setUser(Auth::getUser());
                    $transactionUpload->setAmount($data['totalAmount']);
                    $transactionUpload->setDescription('Funds upload to lender account');
                    $transactionUpload->setTransactionDate(new \DateTime());
                    $transactionUpload->setType(Transaction::FUND_UPLOAD);
                    $transaction1 = $transactionUpload->save($con);

                    $transaction2 = $transaction3 = 1;
                    if ($data['feeAmount'] > 0) {
                        $transactionStripeFee = new Transaction();
                        $transactionStripeFee->setUser(Auth::getUser());
                        $transactionStripeFee->setAmount($stripe_tran_fee);
                        $transactionStripeFee->setDescription('Stripe transaction fee');
                        $transactionStripeFee->setTransactionDate(new \DateTime());
                        $transactionStripeFee->setType(Transaction::STRIPE_FEE);
                        $transaction2 = $transactionStripeFee->save($con);


                        $transactionStripeAdmin = new Transaction();
                        // TODO set use to admin
                        $transactionStripeAdmin->setUser(Auth::getUser());
                        $transactionStripeAdmin->setAmount($data['feeAmount']);
                        $transactionStripeAdmin->setDescription('Lender transaction fee');
                        $transactionStripeAdmin->setTransactionDate(new \DateTime());
                        $transactionStripeAdmin->setType(Transaction::STRIPE_FEE);
                        $transaction3 = $transactionStripeAdmin->save($con);
                    }
                    if ($transaction1 == 1 && $transaction2 == 1 && $transaction3 == 1 ) {
                        $con->commit();
                        Flash::success("Successfully uploaded USD " . $data['totalAmount']);
                        return Redirect::route('lender:history');
                    } else {
                        $con->rollback();
                    }
                }
                // TODO flash message
                Log::error("Stripe error: userid  " . Auth::getUser()->getId());
                Log::error("Stripe error: token   " . $data['stripeToken']);
                // TODO send mail
            }
        }

        Flash::error("Entered Amounts are invalid!");
        return Redirect::route('lender:funds')->withForm($form);
    }
}
