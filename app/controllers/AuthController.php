<?php

use Zidisha\Analytics\MixpanelService;
use Zidisha\Auth\AuthService;
use Zidisha\Auth\Form\LoginForm;
use Zidisha\Borrower\BorrowerGuestQuery;
use Zidisha\Borrower\BorrowerService;
use Zidisha\Borrower\JoinLogQuery;
use Zidisha\Country\CountryQuery;
use Zidisha\User\User;
use Zidisha\User\UserQuery;
use Zidisha\Utility\Utility;
use Zidisha\Vendor\Facebook\FacebookService;
use Zidisha\Vendor\Google\GoogleService;
use Zidisha\Vendor\Mixpanel;
use Zidisha\Vendor\SiftScience\SiftScienceService;

class AuthController extends BaseController
{

    private $facebookService;
    private $authService;
    private $siftScienceService;
    private $googleService;
    
    /**
     * @var Zidisha\Vendor\Mixpanel
     */
    private $mixpanel;
    
    /**
     * @var Zidisha\Borrower\BorrowerService
     */
    private $borrowerService;

    public function __construct(
        FacebookService $facebookService,
        AuthService $authService,
        siftScienceService $siftScienceService,
        GoogleService $googleService,
        MixpanelService $mixpanel,
        BorrowerService $borrowerService
    )
    {
        $this->facebookService = $facebookService;
        $this->authService = $authService;
        $this->siftScienceService = $siftScienceService;
        $this->googleService = $googleService;
        $this->mixpanel = $mixpanel;
        $this->borrowerService = $borrowerService;
    }

    public function getLogin()
    {
        return View::make(Request::ajax() ? 'auth.login-modal' : 'auth.login');
    }

    public function postLogin()
    {
        $rememberMe = Input::has('remember_me');
        $credentials = Input::only('username', 'password');
        
        $form = new LoginForm($this->facebookService, $this->googleService);
        $form->handleRequest(Request::instance());

        if ($form->isValid() && $this->authService->attempt($credentials, $rememberMe)) {
            return $this->login();
        }

        $this->siftScienceService->sendInvalidLoginEvent();

        Flash::error('borrower.login.flash.login-error');
        
        if (Input::get('modal', false)) {
            Session::flash('showLoginModal', true);
            return Redirect::back()->withForm($form);
        }
        
        return Redirect::route('login')->withForm($form);
    }

    public function getLogout()
    {
        $user = \Auth::user();
        
        if ($user) {
            $this->siftScienceService->sendLogoutEvent($user);
        }
        
        $this->flushLogout();
        
        return Redirect::route('home');
    }

    public function getFacebookLogin()
    {
        $facebookUserId = $this->facebookService->getUserId();

        if ($facebookUserId) {
            $checkUser = UserQuery::create()
                ->filterByFacebookId($facebookUserId)
                ->findOne();

            if ($checkUser) {
                Auth::loginUsingId($checkUser->getId());
            } else {
                Flash::error('borrower.login.flash.not-registered-facebook');
                return Redirect::to('login');
            }

            return $this->login();
        } else {
            return Redirect::to('login');
        }
    }

    public function getJoin()
    {
        $country = Utility::getCountryCodeByIP();
        $isBorrowerCountry = CountryQuery::create()
            ->filterByBorrowerCountry(true)
            ->filterByCountryCode($country['code'])
            ->count();

        if ($isBorrowerCountry) {
            return Redirect::route('borrower:join');
        }
        return Redirect::route('lender:join');
    }

    protected function login()
    {
        /** @var User $user */
        $user = \Auth::user();
        if (!$user->isActive()) {
            $this->flushLogout();
            Flash::error('borrower.login.flash.inactive');
            return Redirect::to('login');
        }
        $user->setLastLoginAt(new \DateTime());
        $user->save();

        $this->mixpanel->identify($user);
        $this->mixpanel->trackLoggedIn();
        $this->siftScienceService->sendLoginEvent($user);

        if (Session::get('lenderJoin')) {
            $params = Session::get('lenderJoin');
            Session::forget('lenderJoin');
            return Redirect::route('loan:index', $params);
        }
        if ($user->isLender()) {
            return Redirect::route('lender:dashboard');
        } elseif ($user->isBorrower()) {
            return Redirect::route('borrower:dashboard');
        }

        return Redirect::action('AdminController@getDashboard');
    }

    public function verifyBorrower($verificationCode)
    {
        $joinLog = JoinLogQuery::create()
            ->filterByVerifiedAt(null)
            ->filterByVerificationCode($verificationCode)
            ->findOne();

        if (!$joinLog) {
            \Flash::error('borrower.login.flash.code-not-valid');
            return \Redirect::home();
        }
        
        $borrower = $joinLog->getBorrower();        
        $this->borrowerService->verifyBorrower($borrower);

        \Flash::info('borrower.login.flash.verified');
        if ($borrower->getUser() == \Auth::user()) {
            return Redirect::route('borrower:dashboard');
        }
        
        $this->flushLogout();
        Auth::loginUsingId($borrower->getId());

        return $this->login();
    }

    public function resumeApplication($resumeCode)
    {
        $borrowerGuest = BorrowerGuestQuery::create()
            ->findOneByResumecode($resumeCode);

        if (!$borrowerGuest) {
            \App::abort(404, 'fatal error');
        }

        $form = $borrowerGuest->getForm();
        $form = unserialize($form);

        $session = $borrowerGuest->getSession();
        $session = unserialize($session);

        Session::put('BorrowerJoin', $session);
        Session::put('BorrowerJoin.resumeCode', $resumeCode);

        $profileForm = new \Zidisha\Borrower\Form\Join\ProfileForm();
        $profileForm->handleData($form);

        return Redirect::action('BorrowerJoinController@getProfile')->withForm($profileForm);
    }

    public function postResumeApplication()
    {
        $code = \Input::get('code');

        if (!$code) {
            \App::abort(404, 'fatal error');
        }

        return \Redirect::route('borrower:resumeApplication', [
                'code' => $code
            ]);
    }

    public function getGoogleLogin()
    {
        $accessCode = Input::get('code');

        if ($accessCode) {
            $accessToken = $this->googleService->getAccessToken('google:login', $accessCode);

            if ($accessToken) {
                Session::set('accessToken', $accessToken);
                $googleUser = $this->googleService->getGoogleUserForLogin($accessToken);
                if ($googleUser) {
                    $googleUserId = $googleUser->getId();
                    if ($googleUserId) {
                        $checkUser = UserQuery::create()
                            ->filterByGoogleId($googleUserId)
                            ->findOne();

                        if ($checkUser) {
                            Auth::loginUsingId($checkUser->getId());
                        } else {
                            \Flash::error('borrower.login.flash.not-registered-google');
                            return Redirect::to('login');
                        }
                        return $this->login();
                    }
                }
            }
        }

        \Flash::error('borrower.login.flash.oops');
        return Redirect::to('login');
    }

    protected function flushLogout() {
        Auth::logout();
        Session::flush();
        Session::regenerate();
        $this->facebookService->logout();
    }

}
