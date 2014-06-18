<?php

use Zidisha\User\UserQuery;
use Zidisha\Vendor\Facebook\FacebookService;
use Zidisha\Auth\AuthService;

class AuthController extends BaseController
{

    /**
     * @var Zidisha\Vendor\Facebook\FacebookService
     */
    private $facebookService;

    /**
     * @var Zidisha\Auth\AuthService
     */
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

            if (Auth::getUser()->getRole() == 'lender') {
                return Redirect::route('lender:dashboard');
            }

            if (Auth::getUser()->getRole() == 'borrower') {
                return Redirect::route('borrower:dashboard');
            }

            if (Auth::getUser()->getRole() == 'admin') {
                return Redirect::route('admin:dashboard');
            }
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

            return Redirect::route('home');
        } else {
            return Redirect::to('login');
        }
    }

    public function getJoin()
    {
        // TODO
        return Redirect::route('lender:join');
    }
}
