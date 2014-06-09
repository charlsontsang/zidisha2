<?php

namespace Zidisha\Vendor\Facebook;

class FacebookService
{

    protected $facebook;

    public function __construct()
    {
        $this->facebook = new \Facebook(array(
            'appId' => \Config::get('facebook.app_id'),
            'secret' => \Config::get('facebook.app_secret')
        ));
    }

    public function getLoginUrl($route)
    {
        return $this->facebook->getLoginUrl(
            array('scope' => 'email', 'redirect_uri' => route($route))
        );
    }

    public function isLoggedIn()
    {
        return (boolean) $this->getUserId();
    }
    
    public function logout()
    {
        $this->facebook->destroySession();
    }

    public function getUserId()
    {
        return $this->facebook->getUser();
    }
    
    public function getUserProfile()
    {
        if ($this->isLoggedIn()) {
            return $this->facebook->api('/me');
        }
        
        return null;
    }

}
