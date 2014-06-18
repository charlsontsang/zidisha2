<?php


namespace Zidisha\Admin\Form;


use Zidisha\Country\CountryQuery;
use Zidisha\Form\AbstractForm;

class FilterBorrowers extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'country'      => '',
            'email'         => 'email',
        ];
    }

    public function getDefaultData()
    {

        return [
            'country'      => '',
            'email'     => '',
        ];
    }

    public function getCountries()
    {
        return CountryQuery::create()
            ->orderByName()
            ->find()
            ->toKeyValue('id', 'name');
    }
}
