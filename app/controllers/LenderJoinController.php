<?php

use Zidisha\Lender\Form\Join;
use Zidisha\Utility\Utility;
use Zidisha\Vendor\Facebook\FacebookService;
use Zidisha\User\UserService;

class LenderJoinController extends BaseController
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
     * @var Zidisha\Lender\Form\Join
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

        $country = Utility::getCountryCodeByIP();

        return View::make('lender.join',compact('country') , [
            'form' => $this->joinForm,
            'facebookJoinUrl' => $this->facebookService->getLoginUrl('lender:facebook-join'),
        ]);
    }

    public function postJoin()
    {
        $form = $this->joinForm;
        $form->handleRequest(Request::instance());

        if (!$form->isValid()) {
            return Redirect::route('lender:join')->withForm($form);
        }

        $lender = $this->userService->joinUser($form->getData());

        if ($lender) {
            Auth::login($lender->getUser());
            Flash::success('Welcome to Zidisha!');
            return Redirect::route('lender:dashboard');
        }

        Flash::error('Oops, something went wrong');
        return Redirect::route('lender:join')->withInput();
    }


    public function getFacebookJoin()
    {
        $facebookUser = $this->getFacebookUser();

        if ($facebookUser) {
            return View::make('lender.facebook-join');
        }

        Flash::error('No Facebook account connected.');
        return Redirect::route('lender:join');
    }

    public function postFacebookJoin()
    {
        $facebookUser = $this->getFacebookUser();

        if ($facebookUser) {
            $form = $this->joinForm;
            $form->setFacebookJoin(true);
            $form->handleRequest(Request::instance());

            if (!$form->isValid()) {
                return Redirect::route('lender:facebook-join')->withForm($form);
            }
            
            $this->userService->joinFacebookUser($facebookUser, $form->getData());

            \Auth::loginUsingId($user->getId());

            Flash::success('You have successfully joined Zidisha.');
            return Redirect::route('login');
        } else {
            Flash::error('No Facebook account connected.');
            return Redirect::route('lender:join');
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
                return false;
            }

            return $facebookUser;
        }

        return false;
    }
}