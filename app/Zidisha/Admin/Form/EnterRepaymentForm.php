<?php

namespace Zidisha\Admin\Form;


use Zidisha\Country\CountryQuery;
use Zidisha\Form\AbstractForm;

class EnterRepaymentForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'countryCode' => 'required|in:' . implode(',', array_keys($this->getCountrySlug())),
            'inputFile' => 'required',
        ];
    }

    public function getCountrySlug()
    {
        $countries = array(
            'KE' => 'Kenya',
            'GH' => 'Ghana',
        );

        return $countries;
    }
}
