<?php

use Zidisha\Borrower\VolunteerMentorQuery;
use Zidisha\Country\CountryQuery;
use Zidisha\Utility\Utility;
use Zidisha\Vendor\SiftScience\SiftScienceService;

class BorrowerJoinController extends BaseController
{
    use StepController;

    protected $steps = [
        'country',
        'facebook',
        'profile'
    ];

    protected $facebookService;

    protected $profileForm;

    protected $countryForm;

    protected $borrowerService;

    protected $authService;

    private $borrowerMailer;
    private $siftScienceService;

    public function __construct(
        \Zidisha\Vendor\Facebook\FacebookService $facebookService,
        \Zidisha\Borrower\Form\Join\ProfileForm $profileForm,
        \Zidisha\Borrower\Form\Join\CountryForm $countryForm,
        \Zidisha\Borrower\BorrowerService $borrowerService,
        \Zidisha\Auth\AuthService $authService,
        \Zidisha\Mail\BorrowerMailer $borrowerMailer,
        SiftScienceService $siftScienceService
    ) {
        $this->beforeFilter('@stepsBeforeFilter');
        $this->facebookService = $facebookService;
        $this->profileForm = $profileForm;
        $this->countryForm = $countryForm;
        $this->borrowerService = $borrowerService;
        $this->authService = $authService;
        $this->borrowerMailer = $borrowerMailer;
        $this->siftScienceService = $siftScienceService;

        $this->stepsSessionKey = 'BorrowerJoin';
    }

    public function getCountry()
    {
        $country = Utility::getCountryCodeByIP();

        return View::make('borrower.join.country', compact('country'), ['form' => $this->countryForm]);
    }


    public function postCountry()
    {
        $form = $this->countryForm;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $country = CountryQuery::create()
                ->findOneById($form->getData()['country']);

            Session::put('BorrowerJoin.countryCode', $country->getCountryCode());
            Session::put('BorrowerJoin.countryId', $country->getId());
            $this->setCurrentStep('facebook');

            return Redirect::action('BorrowerJoinController@getFacebook');
        }

        Flash::error('common.validation.incomplete-profile');
        return Redirect::action('BorrowerJoinController@getCountry');
    }


    public function getFacebook()
    {
        return View::make(
            'borrower.join.facebook',
            [
                'facebookJoinUrl' => $this->facebookService->getLoginUrl(
                        'BorrowerJoinController@getFacebookRedirect',
                        ['scope' => 'email,user_location,publish_stream,read_stream']
                    ),
            ]
        );
    }

    public function getFacebookRedirect()
    {
        $facebookUser = $this->facebookService->getUserProfile();

        if ($facebookUser) {
            $errors = $this->borrowerService->validateConnectingFacebookUser($facebookUser);
            $this->facebookService->addFacebookUserLog($facebookUser);

            if ($errors) {
                foreach ($errors as $error) {
                    Flash::error($error);
                }
                return Redirect::action('BorrowerJoinController@getFacebook');
            }

            Session::put('BorrowerJoin.facebookId', $facebookUser['id']);
            Session::put('BorrowerJoin.email', $facebookUser['email']);
            Session::put('BorrowerJoin.facebookData', $facebookUser);
            Session::put('BorrowerJoin.ipAddress', \Request::getClientIp());
            $this->setCurrentStep('profile');
            return Redirect::action('BorrowerJoinController@getProfile');
        }

        Flash::error('borrower.join.facebook-intro');
        return Redirect::action('BorrowerJoinController@getFacebook');
    }

    public function getProfile()
    {
        return View::make(
            'borrower.join.profile',
            ['form' => $this->profileForm,]
        );
    }

    public function postProfile()
    {
        if (Input::has('submit')) {

            $form = $this->profileForm;
            $form->handleRequest(Request::instance());

            if ($form->isValid()) {
                $data = $form->getNestedData();
                $email = $data['email'];
                $data = array_merge($data, Session::get('BorrowerJoin'));
                $data['email'] = $email;
                $borrower = $this->borrowerService->joinBorrower($data);

                $this->flushStepsSession();
                Session::forget('BorrowerJoin');

                if ($form->getIsSaveLater()) {
                    $this->siftScienceService->sendNewBorrowerAccountEvent($borrower, SiftScienceService::NEW_ACCOUNT_TYPE_EDIT);
                } else {
                    $this->siftScienceService->sendNewBorrowerAccountEvent($borrower, SiftScienceService::NEW_ACCOUNT_TYPE_CREATE);
                    $siftScienceScore = $this->siftScienceService->getSiftScore($borrower->getUser());
                    $joinLog = $borrower->getJoinLog();
                    $joinLog->setSiftScienceScore($siftScienceScore);
                    $joinLog->save();
                }

                $this->authService->login($borrower->getUser());

                Flash::success(\Lang::get('common.comments.flash.borrower-join-email-sent'));
                return Redirect::route('borrower:dashboard');
            }

            return Redirect::action('BorrowerJoinController@getProfile')->withForm($form);
        }

        if (Input::has('save-later')) {
            $form = $this->profileForm;
            $form->setIsSaveLater();

            $form->handleRequest(Request::instance());

            if ($form->isValid()) {
                $formData = $form->getDataFromRequest(Request::instance());

                unset($formData['password']);
                unset($formData['_token']);

                return $this->borrowerService->saveBorrowerGuest($formData, Session::get('BorrowerJoin'));
            }

            return Redirect::action('BorrowerJoinController@getProfile')->withForm($form);
        }
        
        if (Input::has('disconnect-facebook')) {
            Session::set('BorrowerJoin.disconnect-facebook', Input::all());
            $url = $this->facebookService->getLogoutUrl('BorrowerJoinController@getFacebook');
            return Redirect::away($url);
        }
        
        return Redirect::back();
    }

    public function getSkipFacebook()
    {
        Session::put('BorrowerJoin.facebookId', null);
        Session::put('BorrowerJoin.ipAddress', \Request::getClientIp());

        if (Session::get('BorrowerJoin.countryCode') == 'BF') {
            $this->setCurrentStep('profile');
            return Redirect::action('BorrowerJoinController@getProfile');
        }

        Flash::error('borrower.join.facebook-intro');
        return Redirect::action('BorrowerJoinController@getCountry');
    }

    public function getVolunteerMentorByCity($city)
    {
        return VolunteerMentorQuery::create()
            ->getVolunteerMentorsByCity($city);
    }
}
