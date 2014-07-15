<?php

namespace Zidisha\Lender\Form;


use Zidisha\Country\CountryQuery;
use Zidisha\Form\AbstractForm;

class Join extends AbstractForm
{
    
    protected $facebookJoin = false;
    protected $googleJoin = false;

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
            'username' => 'required|max:20|unique:users,username',
            'countryId' => ['required', 'in:'.$listOfEnabledCountries]
        ];
        
        if (!$this->facebookJoin && !$this->googleJoin) {
            $rules += [
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|confirmed'
            ];
        } else {
            $rules['aboutMe'] = '';
        }
        
        return $rules;
    }

    public function getCountries()
    {
        $countries = CountryQuery::create()->find();

        return $countries;
    }

}
