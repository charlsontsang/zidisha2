<?php


namespace Zidisha\Admin\Form;


use Zidisha\Country\CountryQuery;
use Zidisha\Form\AbstractForm;

class FilterLenders extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'country'      => '',
            'search'         => '',
        ];
    }

    public function getDefaultData()
    {

        return [
            'country'      => '',
            'search'     => '',
        ];
    }

    public function getCountries()
    {
        $countries =  CountryQuery::create()
            ->orderByName()
            ->find()
            ->toKeyValue('id', 'name');

        return ['all_countries' => 'All Countries'] + $countries;
    }
}
