<?php

namespace Zidisha\Vendor\Google;

use Zidisha\Admin\Setting;

class GoogleService
{

    protected $google;

    public function __construct()
    {
        $google = new \Google_Client();
        $google->setClientId('1040875656394-ol4g61jigqhdauec648sq3ppeu1f1hqi.apps.googleusercontent.com');
        $google->setApplicationName("Client_Library_Examples");
        $google->setClientSecret('AIzaSyDjJf-AC_JeX_i1TF0m98RlQ_F_HYHP36M');
        //$google->setApplicationName('Zidisha.org');
        //$google->setAccessType('online');
        //$google->setDeveloperKey('Zidisha.org');
        $this->google = $google;

    }

    public function getLoginUrl($route, $params = [])
    {
        $this->google->setRedirectUri(strpos($route, '@') === false ? route($route) : action($route));
        //$this->google->setScopes('openid email');
        $this->google->setScopes("https://www.googleapis.com/auth/plus.login");

        return $this->google->createAuthUrl();
    }
}
