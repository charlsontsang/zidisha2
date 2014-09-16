<?php

namespace Zidisha\Auth\Form;

use Zidisha\Form\AbstractForm;
use Zidisha\Vendor\Facebook\FacebookService;
use Zidisha\Vendor\Google\GoogleService;

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

    public function __construct(FacebookService $facebookService, GoogleService $googleService)
    {
        $this->facebookService = $facebookService;
        $this->googleService = $googleService;

        $this->facebookLoginUrl = $this->facebookService->getLoginUrl('facebook:login');
        $this->googleLoginUrl = $this->googleService->getLoginUrl('google:login');
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
}
