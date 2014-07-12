<?php

namespace Zidisha\Vendor\Google;

use Zidisha\Admin\Setting;

class GoogleService
{

    protected $google;

    public function __construct()
    {
        $google = new \Google_Client();
        $google->setClientId(Setting::get('google.clientId'));
        $google->setClientSecret(Setting::get('google.clientSecret'));
        $this->google = $google;

    }

    public function getLoginUrl($route, $params = [])
    {
        $this->google->setRedirectUri(strpos($route, '@') === false ? route($route) : action($route));
        $this->google->setScopes("https://www.googleapis.com/auth/plus.login");

        return $this->google->createAuthUrl();
    }
}
