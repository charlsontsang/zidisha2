<?php

namespace Zidisha\Admin\Form;

use Zidisha\Country\CountryQuery;
use Zidisha\Form\AbstractForm;

class FilterLoans extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'country'      => '',
            'status'         => '',
        ];
    }

    public function getDefaultData()
    {

        return [
            'country'      => '',
            'status'     => '',
        ];
    }

    public function getCountries()
    {
        return CountryQuery::create()
            ->orderByName()
            ->find()
            ->toKeyValue('id', 'name');
    }

    public function getStatus()
    {
        return['fund-raising' => 'Fund Raising', 'active' => 'Active', 'completed' => 'Completed'];
    }
}

