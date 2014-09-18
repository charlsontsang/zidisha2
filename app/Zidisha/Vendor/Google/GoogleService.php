<?php

namespace Zidisha\Vendor\Google;

use DOMDocument;
use DOMXPath;
use Google_Service_Oauth2;
use Google_Service_Oauth2_Userinfoplus;
use Zidisha\Admin\Setting;
use Zidisha\Lender\LenderService;

class GoogleService
{

    protected $google;
    private $lenderService;

    public function __construct(LenderService $lenderService)
    {
        $google = new \Google_Client();
        $google->setClientId(Setting::get('google.clientId'));
        $google->setClientSecret(Setting::get('google.clientSecret'));
        //$google->setApprovalPrompt('force');
        $this->google = $google;
        $this->lenderService = $lenderService;
    }

    public function getLoginUrl($route, $params = [])
    {
        $this->google->setRedirectUri(strpos($route, '@') === false ? route($route) : action($route));
        $this->google->setScopes([
            "https://www.googleapis.com/auth/userinfo.email",
            "https://www.googleapis.com/auth/plus.login",
            "https://www.googleapis.com/auth/contacts.readonly",
        ]);

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
            $errors = $this->lenderService->validateConnectingGoogleUser(
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

    public function getContacts(Google_Service_Oauth2_Userinfoplus $googleUser, $accessToken){

        $contacts = [];
        $i = 0;
        if ($accessToken) {
            $client = new \GuzzleHttp\Client();
            $tokenData = json_decode($accessToken);

            $url = 'https://www.google.com/m8/feeds/contacts/'. urlencode($googleUser->getEmail()) .'/full';
            $res = $client->get($url, [
                    'headers' => [
                        'GData-Version' => '3.0',
                        'Authorization' => "Bearer " . $tokenData->access_token,
                    ]
                ]);

            $doc = new DOMDocument;
            $doc->recover = true;
            $doc->loadXML($res->getBody());

            $xpath = new DOMXPath($doc);
            $xpath->registerNamespace('gd', 'http://schemas.google.com/g/2005');
            $emails = $xpath->query('//gd:email');

            foreach ( $emails as $email )
            {
                $contacts[$i]['name'] = $email->parentNode->getElementsByTagName('title')->item(0)->textContent;
                $contacts[$i]['email'] = $email->getAttribute('address');
                $i++;
            }
        }

        return $contacts;
    }
}
