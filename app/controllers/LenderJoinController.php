<?php

use Zidisha\Lender\Form\JoinForm;
use Zidisha\Lender\InviteVisitQuery;
use Zidisha\Lender\Lender;
use Zidisha\Lender\LenderService;
use Zidisha\Utility\Utility;
use Zidisha\Vendor\Facebook\FacebookService;
use Zidisha\Vendor\Google\GoogleService;

class LenderJoinController extends BaseController
{
    private $facebookService;
    private $joinForm;
    private $lenderService;
    private $googleService;

    public function __construct(
        FacebookService $facebookService,
        JoinForm $joinForm,
        LenderService $lenderService,
        GoogleService $googleService
    ) {
        $this->facebookService = $facebookService;
        $this->joinForm = $joinForm;
        $this->lenderService = $lenderService;
        $this->googleService = $googleService;
    }

    public function getJoin()
    {
        return View::make(Request::ajax() ? 'lender.join-modal' : 'lender.join');
    }

    public function postJoin()
    {
        $form = $this->joinForm;
        $form->handleRequest(Request::instance());

        if (!$form->isValid()) {
            Flash::error('lender.join.flash.oops');
            return Redirect::route('lender:join')->withForm($form);
        }
        
        $user = $this->lenderService->joinLender($form->getData());

        return $this->join($user);
    }

    public function postJoinLend()
    {
        if (Auth::check()) {
            App::abort(404);
        }

        Session::put('lenderJoin.loanId', Input::get('loanId'));
        Session::put('lenderJoin.amount', Input::get('amount'));
        Session::put('lenderJoin.interestRate', Input::get('interestRate'));
        
        // http://stackoverflow.com/questions/22862305/laravel-session-data-not-written-update-availabe-in-view-when-using-ajax
        return Response::json(['success' => true]);
    }

    public function getFacebookJoin()
    {
        $facebookUser = $this->getFacebookUser();

        if ($facebookUser) {
            $country = Utility::getCountryCodeByIP();
            return View::make('lender.facebook-join',
                compact('country'), ['form' => $this->joinForm,]);
        }

        Flash::error('lender.join.flash.facebook-no-account-connected');
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

            $user = $this->lenderService->joinFacebookUser(
                $facebookUser,
                $form->getData()
            );

            return $this->join($user);
        } else {
            Flash::error('comments.flash.welcome');
            return Redirect::route('lender:join');
        }
    }

    private function getFacebookUser()
    {
        $facebookUser = $this->facebookService->getUserProfile();

        if ($facebookUser) {
            $errors = $this->lenderService->validateConnectingFacebookUser(
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
        Auth::login($user->getUser());

        if (Session::get('lenderInviteVisitId')) {
            $lenderInviteVisit = InviteVisitQuery::create()
                ->findOneById(Session::get('lenderInviteVisitId'));
            $inviter = $lenderInviteVisit->getLender()->getUser();

            $this->lenderService->processLenderInvite($user, $lenderInviteVisit);
            Session::forget('lenderInviteVisitId');
            Flash::modal(View::make('lender.invite-new-account', compact('inviter'))->render());
        } else {
            Flash::success('comments.flash.welcome');
        }
        if (Session::get('lenderJoin')) {
            $params = Session::get('lenderJoin');
            Session::forget('lenderJoin');
            return Redirect::route('loan:index', $params);
        }

        return Redirect::route('lender:welcome');
    }

    public function getGoogleJoin()
    {
        $accessCode = Input::get('code');

        if ($accessCode) {
            $accessToken = $this->googleService->getAccessToken('lender:google-join', $accessCode);

            if ($accessToken) {
                Session::set('accessToken', $accessToken);
                $googleUser = $this->googleService->getGoogleUser($accessToken);
                if ($googleUser) {
                    $country = Utility::getCountryCodeByIP();
                    return View::make('lender.google-join',
                        compact('country'), ['form' => $this->joinForm,]);
                }
            }
        }

        Flash::error('lender.join.flash.facebook-no-account-connected');
        return Redirect::route('lender:join');
    }

    public function postGoogleJoin()
    {
        $googleUser = $this->googleService->getGoogleUser(Session::get('accessToken'));

        if ($googleUser) {
            $form = $this->joinForm;
            $form->setGoogleJoin(true);
            $form->handleRequest(Request::instance());

            if (!$form->isValid()) {
                return Redirect::route('lender:google-join')->withForm($form);
            }


            $user = $this->lenderService->joinGoogleUser(
                $googleUser,
                $form->getData()
            );

            $contacts = $this->googleService->getContacts($googleUser, Session::get('accessToken'));
            Session::forget('accessToken');

            $response = $this->join($user);
            if ($contacts) {
                return View::make('lender.invite-google-contacts',
                    compact('contacts'));
            }
            return $response;
        } else {
            Flash::error('comments.flash.welcome');
            return Redirect::route('lender:join');
        }
    }

    public function postGoogleInvite()
    {
        $emails = Input::get('emails');
        $emails = is_array($emails) ? $emails : [];

        $subject = "Join the Global P2P microLending Movement";
        $lender = Auth::user()->getLender();
        $custom_message = "You are Invited to join Zidisha!";

        $countInvites = 0;
        foreach ($emails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            // TODO, remove already existing user and remove emails that are aready invited before
            $countInvites += 1;
            $this->lenderService->lenderInviteViaEmail($lender, $email, $subject, $custom_message);
        }

        Flash::success(\Lang::choice('comments.flash.invite-success', $countInvites, array('count' => $countInvites)));
        return Redirect::route('lender:invite');
    }
}
