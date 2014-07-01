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

    public function getLoginUrl($route, $params = [])
    {
        $defaults = [
            'scope' => 'email',
            'redirect_uri' => strpos($route, '@') === false ? route($route) : action($route),
            'auth_type' => 'reauthenticate',
        ];

        return $this->facebook->getLoginUrl($params + $defaults);
    }

    public function getLogoutUrl($route, $params = [])
    {
        $defaults = [
            'scope' => 'email',
            'redirect_uri' => strpos($route, '@') === false ? route($route) : action($route),
            'auth_type' => 'reauthenticate',
        ];

        return $this->facebook->getLoginUrl($params + $defaults);
    }


    public function isLoggedIn()
    {
        return (boolean)$this->getUserId();
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

    public function hasEnoughFriends()
    {
        $data = $this->facebook->api(
            array(
                'method' => 'fql.query',
                'query' => 'SELECT friend_count FROM user WHERE uid = me()'
            )
        );

        $numberOfFriends = $data[0]['friend_count'];

        //TODO: get minimum number of friends from admin settings.
        if($numberOfFriends > 50){
            return true;
        }
        return false;

    }

    public function isAccountOldEnough()
    {
        //Todo: get date from configuration

        $data = $this->facebook->api('/me/posts?limit=1&until=1388534400');

        if (!empty($data['data']['0'])) {
            return true;
        } else {
            return false;
        }
    }
}
