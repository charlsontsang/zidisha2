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
            'searchInput'         => '',
        ];
    }

    public function getDefaultData()
    {

        return [
            'country'      => '',
            'searchInput'     => '',
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
