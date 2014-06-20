<?php
namespace Zidisha\Borrower\Form\Join;


use Zidisha\Form\AbstractForm;

class Country extends AbstractForm
{

    public function getRules($data)
    {
        $countries = $this->getCountries()->toKeyValue('id', 'id');
        $listOfEnabledCountries= implode(",", $countries);

        return [
            'country' => ['required', 'in:'.$listOfEnabledCountries]
        ];
    }

    public function getCountries()
    {
        $countries = \Zidisha\Country\CountryQuery::create()
            ->filterByBorrowerCountry(1)
            ->find();

        return $countries;
    }
}