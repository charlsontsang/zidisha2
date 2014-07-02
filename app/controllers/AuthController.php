<?php

use Zidisha\Auth\AuthService;
use Zidisha\Borrower\JoinLogQuery;
use Zidisha\User\UserQuery;
use Zidisha\Vendor\Facebook\FacebookService;
use Zidisha\Vendor\Mixpanel;

class AuthController extends BaseController
{

    private $facebookService;
    private $authService;

    public function __construct(FacebookService $facebookService, AuthService $authService)
    {
        $this->facebookService = $facebookService;
        $this->authService = $authService;
    }

    public function getLogin()
    {
        return View::make(
            'auth.login',
            [
                'facebookLoginUrl' => $this->facebookService->getLoginUrl('facebook:login'),
            ]
        );
    }

    public function postLogin()
    {
        $rememberMe = Input::has('remember_me');
        $credentials = Input::only('username', 'password');

        if ($this->authService->attempt($credentials, $rememberMe)) {
            return $this->login();
        }

        Flash::error("Wrong username or password!");
        return Redirect::route('login');
    }

    public function getLogout()
    {
        Auth::logout();
        Session::flush();
        Session::regenerate();
        $this->facebookService->logout();
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
                Flash::error('You are not registered to use Facebook. Please sign up with Facebook first.');
                return Redirect::to('login');
            }

            return $this->login();
        } else {
            return Redirect::to('login');
        }
    }

    public function getJoin()
    {
        // TODO
        return Redirect::route('lender:join');
    }

    protected function login()
    {
        $user = \Auth::user();
        $role = $user->getRole();

        Mixpanel::identify(
            $user->getId(),
            array(
                'username' => $user->getUsername(),
                'userlevel' => $role,
                'email' => $user->getEmail(),
            )
        );
        Mixpanel::track('Logged in');

        if ($role == 'lender') {
            return Redirect::route('lender:dashboard');
        } elseif ($role == 'borrower') {
            return Redirect::route('borrower:dashboard');
        }

        return Redirect::route('admin:dashboard');
    }

    public function verifyBorrower($verificationCode)
    {
        $joinLog = JoinLogQuery::create()
            ->filterByVerifiedAt(null)
            ->filterByVerificationCode($verificationCode)
            ->findOne();

        if (!$joinLog) {
            \Flash::error('The code is not valid');
            return \Redirect::home();
        }

        $joinLog
            ->setVerifiedAt(new \DateTime());
        $joinLog->save();
        
        $borrower = $joinLog->getBorrower();
        $borrower
            ->setVerified(1);

        $borrower->save();

        Auth::loginUsingId($borrower->getId());

        \Flash::info('You are verified.');
        return $this->login();
    }
}
