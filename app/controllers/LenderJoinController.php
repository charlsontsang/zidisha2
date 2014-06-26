<?php

use Zidisha\Lender\Form\Join;
use Zidisha\Lender\InviteVisitQuery;
use Zidisha\Lender\Lender;
use Zidisha\Lender\LenderService;
use Zidisha\Utility\Utility;
use Zidisha\Vendor\Facebook\FacebookService;
use Zidisha\User\UserService;

class LenderJoinController extends BaseController
{
    private $facebookService;
    private $userService;
    private $joinForm;
    private $lenderService;

    public function __construct(
        FacebookService $facebookService,
        UserService $userService,
        Join $joinForm,
        LenderService $lenderService
    ) {
        $this->facebookService = $facebookService;
        $this->userService = $userService;
        $this->joinForm = $joinForm;
        $this->lenderService = $lenderService;
    }

    public function getJoin()
    {

        $country = Utility::getCountryCodeByIP();

        return View::make(
            'lender.join',
            compact('country'),
            [
                'form' => $this->joinForm,
                'facebookJoinUrl' => $this->facebookService->getLoginUrl(
                        'lender:facebook-join'
                    ),
            ]
        );
    }

    public function postJoin()
    {
        $form = $this->joinForm;
        $form->handleRequest(Request::instance());

        if (!$form->isValid()) {
            Flash::error('Oops, something went wrong');
            return Redirect::route('lender:join')->withForm($form);
        }

        $user = $this->userService->joinUser($form->getData());

        return $this->join($user);
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

            $user = $this->userService->joinFacebookUser(
                $facebookUser,
                $form->getData()
            );

            return $this->join($user);
        } else {
            Flash::error(\Lang::get('comments.flash.welcome'));
            return Redirect::route('lender:join');
        }
    }

    private function getFacebookUser()
    {
        $facebookUser = $this->facebookService->getUserProfile();

        if ($facebookUser) {
            $errors = $this->userService->validateConnectingFacebookUser(
                $facebookUser
            );

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

    protected function join(Lender $user)
    {
        if (Session::get('lenderInviteVisitId')) {
            $lenderInviteVisit = InviteVisitQuery::create()
                ->findOneById(Session::get('lenderInviteVisitId'));
            $inviter = $lenderInviteVisit->getLender()->getUser();

            $this->lenderService->processLenderInvite($user, $lenderInviteVisit);
            Session::forget('lenderInviteVisitId');
            Flash::modal(View::make('lender.invite-new-account', compact('inviter'))->render());
        } else {
            Flash::success(\Lang::get('comments.flash.welcome'));
        }

        Auth::login($user->getUser());
        return Redirect::route('lender:dashboard');
    }
}