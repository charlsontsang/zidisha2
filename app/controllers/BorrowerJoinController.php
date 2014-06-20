<?php

use Zidisha\Utility\Utility;

class BorrowerJoinController extends BaseController
{
    use StepController;

    protected $steps = [
        'country',
        'facebook',
        'profile'
    ];


    protected $facebookService;

    protected $userService;

    protected $profileForm;

    protected $countryForm;

    protected $borrowerService;

    protected $authService;

    public function __construct(
        \Zidisha\Vendor\Facebook\FacebookService $facebookService,
        \Zidisha\User\UserService $userService,
        \Zidisha\Borrower\Form\Join\Profile $profileForm,
        \Zidisha\Borrower\Form\Join\Country $countryForm,
        \Zidisha\Borrower\BorrowerService $borrowerService,
        \Zidisha\Auth\AuthService $authService
    ) {
        $this->beforeFilter('@stepsBeforeFilter');
        $this->facebookService = $facebookService;
        $this->userService = $userService;
        $this->profileForm = $profileForm;
        $this->countryForm = $countryForm;
        $this->borrowerService = $borrowerService;
        $this->authService = $authService;
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
            Session::put('BorrowerJoin.country', $form->getData()['country']);
            $this->setCurrentStep('facebook');

            return Redirect::action('BorrowerJoinController@getFacebook');
        }

        Flash::error('You can only select from list of available countries.');
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
            $errors = $this->userService->validateConnectingFacebookUser($facebookUser);

            if ($errors) {
                foreach ($errors as $error) {
                    Flash::error($error);
                }
                return Redirect::action('BorrowerJoinController@getFacebook');
            }

            Session::put('BorrowerJoin.facebook_id', $facebookUser['id']);
            Session::put('BorrowerJoin.email', $facebookUser['email']);

            $this->setCurrentStep('profile');
            return Redirect::action('BorrowerJoinController@getProfile');
        }

        Flash::error('You need to provide a Valid Facebook Profile');
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
                $data = $form->getData();

                $data = array_merge($data, Session::get('BorrowerJoin'));

                $borrower = $this->borrowerService->joinBorrower($data);

                $this->flushStepsSession();
                Session::forget('BorrowerJoin');

                $this->authService->login($borrower->getUser());

                return Redirect::route('borrower:dashboard');
            }

            return Redirect::action('BorrowerJoinController@getProfile')->withForm($form);
        }

        if (Input::has('save_later')) {
            dd(Input::all());
        }

        if (Input::has('diconnect_facebook_account')) {
            Session::set('BorrowerJoin.disconnect-facebook', Input::all());
            $url = $this->facebookService->getLogoutUrl('BorrowerJoinController@getFacebook');
            return Redirect::away($url);
        }
    }
}