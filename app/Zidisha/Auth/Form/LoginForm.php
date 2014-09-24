<?php

namespace Zidisha\Auth\Form;

use Zidisha\Form\AbstractForm;
use Zidisha\Vendor\Facebook\FacebookService;
use Zidisha\Vendor\Google\GoogleService;
use Zidisha\Utility\Utility;
use Zidisha\Country\CountryQuery;

class LoginForm extends AbstractForm
{
    /**
     * @var \Zidisha\Vendor\Facebook\FacebookService
     */
    private $facebookService;

    /**
     * @var \Zidisha\Vendor\Google\GoogleService
     */
    private $googleService;

    protected $facebookLoginUrl;

    protected $googleLoginUrl;

    protected $joinLink;

    public function __construct(FacebookService $facebookService, GoogleService $googleService, $isLenderCountry = false)
    {
        $this->facebookService = $facebookService;
        $this->googleService = $googleService;

        if ($isLenderCountry) {
            $this->facebookLoginUrl = $this->facebookService->getLoginUrl('facebook:login', [], true);
        } else {
            $this->facebookLoginUrl = $this->facebookService->getLoginUrl('facebook:login');
        }
        $this->googleLoginUrl = $this->googleService->getLoginUrl('google:login');
        $this->joinLink = $this->getJoinLink();
    }

    public function getRules($data)
    {
        return [
            'email'    => 'required',
            'password' => 'required',
        ];
    }

    /**
     * @return string
     */
    public function getFacebookLoginUrl()
    {
        return $this->facebookLoginUrl;
    }

    /**
     * @return string
     */
    public function getGoogleLoginUrl()
    {
        return $this->googleLoginUrl;
    }

    /**
     * @return string
     */
    public function getjoinLink()
    {
        $country = Utility::getCountryByIP();
        if($country && $country->isBorrowerCountry()) {
            return 'borrower:join';
        }
        return 'join';
    }
}
