<?php
namespace Zidisha\Borrower\Form;


use Zidisha\Form\AbstractForm;

class checkCountry extends AbstractForm
{

    public function getRules($data)
    {
        $countries = $this->getEnabledCountries()->toKeyValue('id', 'id');
        $listOfEnabledCountries= implode(",", $countries);

        return [
            'country' => ['required', 'in:'.$listOfEnabledCountries]
        ];
    }

    public function getEnabledCountries()
    {
        $countries = \Zidisha\Country\CountryQuery::create()
            ->filterByEnabled(1)
            ->find();

        return $countries;
    }
}