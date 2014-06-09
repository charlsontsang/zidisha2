<?php

class AuthController extends BaseController
{
    public function getJoin()
    {
        $facebook = new Facebook(array(
            'appId' => Config::get('facebook.app_id'),
            'secret' => Config::get('facebook.app_secret')
        ));

        $data['facebookJoinUrl'] = $facebook->getLoginUrl(
            array('scope' => 'email', 'redirect_uri' => route('facebook:join'))
        );

        return View::make('auth.join', $data);
    }

    public function postJoin()
    {
        $rules = [
            'email' => 'required|email',
            'username' => 'required|max:20',
            'password' => 'required|confirmed'
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Redirect::to('join')->withInput()->withErrors($validator);
        }

        $user = new User();
        $user->setPassword(Input::get('password'));
        $user->setEmail(Input::get('email'));
        $user->setUsername(Input::get('username'));

        if ($user->save()) {
            Auth::login($user);
            return Redirect::route('home');
        }

        return Redirect::to('join')->withInput();
    }

    public function getLogin()
    {
        $facebook = new Facebook(array(
            'appId' => Config::get('facebook.app_id'),
            'secret' => Config::get('facebook.app_secret')
        ));

        $data = [
            'facebookLoginUrl' => $facebook->getLoginUrl(
                array('scope' => 'email', 'redirect_uri' => route('facebook:login'))
            )
        ];

        return View::make('auth.login', $data);
    }

    public function postLogin()
    {
        $rememberMe = Input::has('remember_me');

        $input = array(
            'username' => Input::get('username'),
            'password' => Input::get('password'),
        );

        if (Auth::attempt($input, $rememberMe)) {
            return Redirect::route('home');
        }

        Flash::error("Wrong username or password!");
        return Redirect::route('login');
    }

    public function getLogout()
    {
        Auth::logout();
        $facebook = new Facebook(array(
            'appId' => Config::get('facebook.app_id'),
            'secret' => Config::get('facebook.app_secret')
        ));
        $facebook->destroySession();
        return Redirect::route('home');
    }

    public function getFacebookJoin()
    {
        $facebookUser = $this->getFacebookUser();

        if (Session::has('error')) {
            $app_id = Config::get('facebook.app_id');
            $app_secret = Config::get('facebook.app_secret');

            $facebook = new Facebook(array(
                'appId' => $app_id,
                'secret' => $app_secret
            ));

            $data['facebookJoinUrl'] = $facebook->getLoginUrl(
                array('scope' => 'email', 'redirect_uri' => route('facebook:join'))
            );

            return View::make('auth.join', $data);
        }
        if ($facebookUser) {
            return View::make('auth.confirm');
        }

        return Redirect::to('join');
    }

    public function postFacebookConfirm()
    {
        $facebookUser = $this->getFacebookUser();

        if ($facebookUser) {

            $rules = [
                'username' => 'required|max:20'
            ];

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails()) {
                return Redirect::route('facebook:join')->withErrors($validator);
            }

            $user = new User();
            $user
                ->setUsername(Input::get('username'))
                ->setEmail($facebookUser['email'])
                ->setFacebookId($facebookUser['id']);

            $lender = new Lender();
            $lender
                ->setUser($user)
                ->setFirstName($facebookUser['first_name'])
                ->setLastName($facebookUser['last_name'])
                ->setAboutMe(Input::get('about_me'))
                // TODO
                //->setCountry($facebookUser['location']);
                ->setCountryId(1);

            $lender->save();

            Auth::loginUsingId($user->getId());

            return Redirect::to('login')->with('success', 'You have successfully joined Zidisha.');
        } else {
            return Redirect::to('join')->with('error', 'No Facebook account connected.');
        }
    }

    private function getFacebookUser()
    {
        $facebook = new Facebook(array(
            'appId' => Config::get('facebook.app_id'),
            'secret' => Config::get('facebook.app_secret')
        ));

        $facebookUser = $facebook->getUser();

        if ($facebookUser) {
            try {
                // Proceed knowing you have a logged in user who's authenticated.
                if ($userProfile = $facebook->api('/me')) {
                    $checkUser = UserQuery::create()
                        ->filterByFacebookId($userProfile['id'])
                        ->_or()
                        ->filterByEmail($userProfile['email'])
                        ->findOne();

                    if ($checkUser) {
                        if ($checkUser->getFacebookId() == $userProfile['id']) {
                            return Redirect::to('join')->with(
                                'error',
                                'This facebook account already linked with another account on our website.'
                            );
                        } else {
                            return Redirect::to('join')->with(
                                'error',
                                'The email address linked to the facebook account is already linked with another account on our website.'
                            );
                        }
                    }

                    return $userProfile;
                }
            } catch (FacebookApiException $e) {

            }
        }

        return false;
    }

    public function getFacebookLogin()
    {
        $facebook = new Facebook(array(
            'appId' => Config::get('facebook.app_id'),
            'secret' => Config::get('facebook.app_secret')
        ));

        $facebookUserId = $facebook->getUser();

        if ($facebookUserId) {
            $checkUser = UserQuery::create()
                ->filterByFacebookId($facebookUserId)
                ->findOne();

            if ($checkUser) {
                Auth::loginUsingId($checkUser->getId());
            } else {
                return Redirect::to('login')->with(
                    'error',
                    'You are not registerd to sue facebook. Please sign up with facebook first.'
                );
            }

            return Redirect::route('home');
        } else {
            return Redirect::to('login');
        }
    }
}
