<?php
namespace Zidisha\Auth;


use Zidisha\User\User;

class AuthService
{
    public function login(User $user)
    {
        \Auth::login($user);
    }


    public function attempt($credentials, $rememberMe)
    {
        return \Auth::attempt($credentials, $rememberMe);
    }
} 