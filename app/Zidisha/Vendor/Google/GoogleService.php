<?php

namespace Zidisha\Vendor\Google;

use Google_Service_Oauth2;
use Illuminate\Http\Request;
use Zidisha\Admin\Setting;
use Zidisha\User\UserService;

class GoogleService
{

    protected $google;
    private $userService;

    public function __construct(UserService $userService)
    {
        $google = new \Google_Client();
        $google->setClientId(Setting::get('google.clientId') . '.apps.googleusercontent.com');
        $google->setClientSecret(Setting::get('google.clientSecret'));
        //$google->setApprovalPrompt('force');
        $this->google = $google;

        $this->userService = $userService;
    }

    public function getLoginUrl($route, $params = [])
    {
        $this->google->setRedirectUri(strpos($route, '@') === false ? route($route) : action($route));
        $this->google->setScopes(["https://www.googleapis.com/auth/userinfo.email", "https://www.googleapis.com/auth/plus.login"]);

        return $this->google->createAuthUrl();
    }

    public function getAccessToken($route, $code)
    {
        $this->google->setRedirectUri(strpos($route, '@') === false ? route($route) : action($route));
        $this->google->authenticate($code);
        return $this->google->getAccessToken();
    }

    public function getUserProfile($accessToken)
    {
        $this->google->setAccessToken($accessToken);
        $service = new Google_Service_Oauth2($this->google);
        $user = $service->userinfo->get();
        return $user;
    }

    public function getGoogleUser($accessToken)
    {
        $googleUser = $this->getUserProfile($accessToken);

        if ($googleUser) {
            $errors = $this->userService->validateConnectingGoogleUser(
                $googleUser
            );

            if ($errors) {
                foreach ($errors as $error) {
                    \Flash::error($error);
                }
                return false;
            }

            return $googleUser;
        }

        return false;
    }

    public function getGoogleUserForLogin($accessToken){

        $googleUser = $this->getUserProfile($accessToken);

        if ($googleUser) {
            return $googleUser;
        }
        return false;
    }
}
