<?php

use Zidisha\Auth\Form\Join;
use Zidisha\Lender\Lender;
use Zidisha\User\User;
use Zidisha\User\UserQuery;
use Zidisha\User\UserService;
use Zidisha\Vendor\Facebook\FacebookService;

class AuthController extends BaseController
{

    /**
     * @var Zidisha\Vendor\Facebook\FacebookService
     */
    private $facebookService;
    
    /**
     * @var Zidisha\User\UserService
     */
    private $userService;
    /**
     * @var Zidisha\Auth\Form\Join
     */
    private $joinForm;

    public function __construct(FacebookService $facebookService, UserService $userService, Join $joinForm)
    {
        $this->facebookService = $facebookService;
        $this->userService = $userService;
        $this->joinForm = $joinForm;
    }
    
    public function getJoin()
    {        
        return View::make('auth.join', [
            'form' => $this->joinForm,
            'facebookJoinUrl' => $this->facebookService->getLoginUrl('facebook:join'),
        ]);
    }

    public function postJoin()
    {
        $form = $this->joinForm;
        $form->handleRequest(Request::instance());

        if (!$form->isValid()) {
            return Redirect::to('join')->withForm($form);
        }

        $user = $this->userService->joinUser($form->getData());

        if ($user) {
            Auth::login($user);
            return Redirect::route('lender:public-profile');
        }

        Flash::error('Oops, something went wrong');
        return Redirect::to('join')->withInput();
    }

    public function getLogin()
    {
        return View::make('auth.login', [
            'facebookLoginUrl' => $this->facebookService->getLoginUrl('facebook:login'),
        ]);
    }

    public function postLogin()
    {
        $rememberMe = Input::has('remember_me');
        $credentials = Input::only('username', 'password');
        
        if (Auth::attempt($credentials, $rememberMe)) {
            return Redirect::route('home');
        }

        Flash::error("Wrong username or password!");
        return Redirect::route('login');
    }

    public function getLogout()
    {
        Auth::logout();
        $this->facebookService->logout();
        return Redirect::route('home');
    }

    public function getFacebookJoin()
    {
        $facebookUser = $this->getFacebookUser();

        if ($facebookUser) {
            return View::make('auth.confirm');
        }

        Flash::error('No Facebook account connected.'); 
        return Redirect::to('join');
    }

    public function postFacebookConfirm()
    {
        $facebookUser = $this->getFacebookUser();

        if ($facebookUser) {
            $form = $this->joinForm;
            $form->setFacebookJoin(true);
            $form->handleRequest(Request::instance());

            if (!$form->isValid()) {
                return Redirect::route('facebook:join')->withForm($form);
            }

            $this->userService->joinFacebookUser($facebookUser, $form->getData());

            Flash::success('You have successfully joined Zidisha.');
            return Redirect::to('login');
        } else {
            Flash::error('No Facebook account connected.');
            return Redirect::to('join');
        }
    }

    private function getFacebookUser()
    {
        $facebookUser = $this->facebookService->getUserProfile();
        
        if ($facebookUser) {
            $errors = $this->userService->validateConnectingFacebookUser($facebookUser);

            if ($errors) {
                foreach ($errors as $error) {
                    Flash::error($error);
                }
                return Redirect::to('join');
            }

            return $facebookUser;
        }
        
        return false;
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
                return Redirect::to('login')->with(
                    'error',
                    'You are not registered to use Facebook. Please sign up with Facebook first.'
                );
            }

            return Redirect::route('home');
        } else {
            return Redirect::to('login');
        }
    }
}
