<?php

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

    protected $joinForm;

    public function __construct(\Zidisha\Vendor\Facebook\FacebookService $facebookService, \Zidisha\User\UserService $userService, \Zidisha\Borrower\Form\Join $joinForm)
    {
        $this->beforeFilter('@stepsBeforeFilter');
        $this->facebookService = $facebookService;
        $this->userService = $userService;
        $this->joinForm = $joinForm;
    }

    public function getCountry()
    {
        $countries = $this->getEnabledCountries();

        return View::make('borrower.join.country', ['countries' => $countries]);
    }


    public function postCountry()
    {
        $inputCountry = Input::get('country');
        $isCountryValid = false;

        $countryData = array();
        $countries = \Zidisha\Country\CountryQuery::create()
            ->filterByEnabled(1)
            ->find();

        foreach ($countries as $country) {
            if ($inputCountry == $country->getId()) {
                $isCountryValid = true;
                Session::put('BorrowerJoin.country', $country->getId());
            }
        }

        if ($isCountryValid == false) {
            Flash::error('You can only select from list of available countries.');
            return Redirect::action('BorrowerJoinController@getCountry');
        }

        $this->setCurrentStep('facebook');

        return Redirect::action('BorrowerJoinController@getFacebook');
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
        $facebookUser = $this->getFacebookUser();

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
        $email = Session::get('BorrowerJoin.email');
        return View::make(
            'borrower.join.profile',
            ['form' => $this->joinForm,'email' => $email]
        );
    }

    public function postProfile()
    {
        if (Input::has('submit')) {

            $form = $this->joinForm;
            $form->handleRequest(Request::instance());

            if ($form->isValid()) {
                $data = $form->getData();

                $data = array_merge($data, Session::get('BorrowerJoin'));

                $user = new Zidisha\User\User();
                $user->setUsername($data['username']);
                $user->setEmail($data['email']);
                $user->setFacebookId($data['facebook_id']);
                $user->setRole('borrower');


                $borrower = new Zidisha\Borrower\Borrower();
                $borrower->setFirstName($data['first_name']);
                $borrower->setLastName($data['last_name']);
                $borrower->setCountryId($data['country']);
                $borrower->setUser($user);

                $borrowerProfile = new Zidisha\Borrower\Profile();
                $borrowerProfile->setBorrower($borrower);
                $borrowerProfile->save();

                $this->flushStepsSession();
                Session::forget('BorrowerJoin');

                Auth::login($user);

                return  Redirect::route('borrower:dashboard');
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

    private function getEnabledCountries()
    {
        $countries = \Zidisha\Country\CountryQuery::create()
            ->filterByEnabled(1)
            ->find();

        return $countries->toKeyValue('id', 'name');
    }

    private function getFacebookUser()
    {
        $facebookUser = $this->facebookService->getUserProfile();

        if ($facebookUser) {
            return $facebookUser;
        }

        return false;
    }
}