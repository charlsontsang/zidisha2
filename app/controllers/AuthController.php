<?php

class AuthController extends BaseController
{
    public function getJoin()
    {
        return View::make('auth.join');
    }

    public function postJoin()
    {
        $rules = [
            'email' => 'required|email',
            'username' => 'required|max:20',
            'password' => 'required|confirmed'
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails())
        {
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
        return View::make('auth.login');
    }

    public function postLogin()
    {
        $rememberMe = Input::has('remember_me');

        $input = array(
            'username' => Input::get('username'),
            'password' => Input::get('password'),
            'email' => Input::get('email')
        );

        if (Auth::attempt($input, $rememberMe)) {
            return Redirect::route('home');
        }

        return Redirect::route('login');
    }

    public function getLogout()
    {
        Auth::logout();
        return Redirect::route('home');
    }
}
