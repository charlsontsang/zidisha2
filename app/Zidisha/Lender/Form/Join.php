<?php

namespace Zidisha\Lender\Form;


use Zidisha\Form\AbstractForm;

class Join extends AbstractForm
{
    
    protected $facebookJoin = false;

    /**
     * @param boolean $facebookJoin
     */
    public function setFacebookJoin($facebookJoin)
    {
        $this->facebookJoin = $facebookJoin;
    }

    /**
     * @return boolean
     */
    public function isFacebookJoin()
    {
        return $this->facebookJoin;
    }

    public function getRules($data)
    {
        $countries = $this->getCountries()->toKeyValue('id', 'id');
        $listOfEnabledCountries= implode(",", $countries);
        
        $rules = [
            'username' => 'required|max:20|unique:users,username',
            'countryId' => ['required', 'in:'.$listOfEnabledCountries]
        ];
        
        if (!$this->isFacebookJoin()) {
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
        $countries = \Zidisha\Country\CountryQuery::create()->find();

        return $countries;
    }

}
