<?php
namespace Zidisha\Borrower\Form\Join;


use Zidisha\Country\CountryQuery;
use Zidisha\Form\AbstractForm;

class ProfileForm extends AbstractForm
{
    protected $country;

    public function getRules($data)
    {
        $phoneNumberLength = $this->getCountry()->getPhoneNumberLength();
        
        return [
            'username'             => 'required|unique:users,username',
            'password'             => 'required',
            'email'                => 'required|email|unique:users,email',
            'firstName'            => 'required',
            'lastName'             => 'required',
            'address'              => 'required',
            'addressInstruction'   => 'required',
            'city'                 => 'required',
            'nationalIdNumber'     => 'required|unique:borrower_profiles,national_id_number',
            'phoneNumber'          => 'required|numeric|digits:' . $phoneNumberLength,
            'alternatePhoneNumber' => 'required|numeric' . $phoneNumberLength,
        ];
    }

    public function getDefaultData()
    {
        return [
            'email' => \Session::get('BorrowerJoin.email'),
        ];
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        if ($this->country === null) {
            $this->country = CountryQuery::create()
                ->findOneByCountryCode(\Session::get('BorrowerJoin.countryCode'));
        }
        
        return $this->country;
    }
}