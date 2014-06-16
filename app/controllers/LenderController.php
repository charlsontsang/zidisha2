<?php

use Illuminate\Support\Facades\View;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Lender\Form\EditProfile;
use Zidisha\Lender\LenderQuery;
use Zidisha\Lender\ProfileQuery;

class LenderController extends BaseController
{
    protected $transactionQuery;

    /**
     * @var Zidisha\Lender\Form\EditProfile
     */
    private $editProfileForm;

    public function __construct(EditProfile $editProfileForm, TransactionQuery $transactionQuery)
    {
        $this->editProfileForm = $editProfileForm;
        $this->transactionQuery = $transactionQuery;
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

            $lender->setFirstName($data['firstName']);
            $lender->setLastName($data['lastName']);
            $lender->getUser()->setEmail($data['email']);
            $lender->getUser()->setUsername($data['username']);
            $lender->getProfile()->setAboutMe($data['aboutMe']);

            if (!empty($data['password'])) {
                $lender->getUser()->setPassword($data['password']);
            }

            $lender->save();

            if(Input::hasFile('picture'))
            {
                $image = Input::file('picture');
                $image->move(public_path() . '/images/profile/', $data['username'].'.jpg' );
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

        $currentBalancePageObj = DB::select('SELECT SUM(amount) AS total FROM transactions
WHERE id IN (SELECT id FROM transactions WHERE transactions.USER_ID=?
ORDER BY transactions.TRANSACTION_DATE DESC OFFSET ?)', array(Auth::getUser()->getId(), (($page-1)*3)));

        $currentBalancePage = $currentBalancePageObj[0]->total;

        $paginator = $this->transactionQuery->create()
            ->orderByTransactionDate('desc')
            ->filterByUserId(Auth::getUser()->getId())
            ->paginate($page, 3);

        return View::make('lender.history', compact('paginator', 'currentBalance', 'currentBalancePage'));
    }
}
