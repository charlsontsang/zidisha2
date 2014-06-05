<?php

class AuthController extends BaseController
{
    public function getJoin()
    {
        return View::make('auth.join');
    }

    public function postJoin()
    {
        $user = new User();
        $user->setPassword(Input::get('password'));
        $user->setEmail(Input::get('email'));
        $user->setUsername(Input::get('username'));

        if ($user->save()) {
            Auth::login($user);
            return Redirect::route('home');
        }

        return Redirect::route('join');
    }

    public function getLogin()
    {
        return View::make('auth.login');
    }

    public function postLogin()
    {
        $input = array(
            'username' => Input::get('username'),
            'password' => Input::get('password'),
            'email' => Input::get('email')
        );

        if (Auth::attempt($input)) {
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
