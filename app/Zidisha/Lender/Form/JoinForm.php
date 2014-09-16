<?php

namespace Zidisha\Lender\Form;


use Zidisha\Country\CountryQuery;
use Zidisha\Form\AbstractForm;
use Zidisha\Utility\Utility;
use Zidisha\Vendor\Facebook\FacebookService;
use Zidisha\Vendor\Google\GoogleService;

class JoinForm extends AbstractForm
{
    
    protected $facebookJoin = false;
    protected $googleJoin = false;
    
    protected $country;
    protected $facebookJoinUrl;
    protected $googleJoinUrl;
    
    /**
     * @var \Zidisha\Vendor\Facebook\FacebookService
     */
    private $facebookService;
    
    /**
     * @var \Zidisha\Vendor\Google\GoogleService
     */
    private $googleService;

    public function __construct(FacebookService $facebookService, GoogleService $googleService)
    {
        $this->country = Utility::getCountryCodeByIP();
        $this->facebookJoinUrl = $facebookService->getLoginUrl('lender:facebook-join');
        $this->googleJoinUrl  = $googleService->getLoginUrl('lender:google-join') . '&max_auth_age=0';
        $this->facebookService = $facebookService;
        $this->googleService = $googleService;
    }

    public function setFacebookJoin($facebookJoin)
    {
        $this->facebookJoin = $facebookJoin;
    }

    public function setGoogleJoin($googleJoin)
    {
        $this->googleJoin = $googleJoin;
    }

    public function getRules($data)
    {
        $countries = $this->getCountries()->toKeyValue('id', 'id');
        $listOfEnabledCountries= implode(",", $countries);
        
        $rules = [
            'username'  => 'required|max:20|alpha_num_space',
            'countryId' => ['required', 'in:'.$listOfEnabledCountries]
        ];
        
        if (!$this->facebookJoin && !$this->googleJoin) {
            $rules += [
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|min:7'
            ];
        } else {
            $rules['aboutMe'] = '';
        }
        
        return $rules;
    }

    public function getDefaultDate()
    {
        return [
            'countryId' => $this->country['id'], 
        ];
    }

    public function getCountries()
    {
        $countries = CountryQuery::create()->find();

        return $countries;
    }

    /**
     * @return array
     */
    public function getCountry()
    {
        return $this->country;
    }
    
    /**
     * @return string
     */
    public function getFacebookJoinUrl()
    {
        return $this->facebookJoinUrl;
    }

    /**
     * @return string
     */
    public function getGoogleJoinUrl()
    {
        return $this->googleJoinUrl;
    }

}
