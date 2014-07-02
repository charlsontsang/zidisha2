<?php

namespace Zidisha\Admin\Form;

use Zidisha\Country\CountryQuery;
use Zidisha\Form\AbstractForm;

class ExchangeRateForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'countrySlug' => 'required',
            'newRate' => 'required|numeric',
        ];
    }

    public function getDefaultData()
    {

    }

    public function getCountrySlug()
    {
        $currencies = [];
        $countries = CountryQuery::create()
            ->filterByBorrowerCountry(true)
            ->orderByName()
            ->find();

        foreach ($countries as $country) {
            $currencies[$country->getSlug()] = $country->getCurrency()->getName() . " in " . $country->getName();
        }

        return $currencies;
    }
}