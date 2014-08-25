<?php


namespace Zidisha\Admin\Form;


use Zidisha\Borrower\Borrower;
use Zidisha\Country\CountryQuery;
use Zidisha\Form\AbstractForm;

class FilterBorrowers extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'country'      => '',
            'search'         => '',
            'status' => ''
        ];
    }

    public function getDefaultData()
    {

        return [
            'country'      => '',
            'search'     => '',
            'status' => ''
        ];
    }

    public function getCountries()
    {
        $countries =  CountryQuery::create()
            ->orderByName()
            ->filterByBorrowerCountry(true)
            ->find()
            ->toKeyValue('id', 'name');

        return ['all_countries' => 'All Countries'] + $countries;
    }

    public function getStatus()
    {
        return [
            'all' => 'All',
            Borrower::ACTIVATION_INCOMPLETE => 'Pending Submission',
            Borrower::ACTIVATION_PENDING => 'Pending Activation',
            Borrower::ACTIVATION_DECLINED => 'Declined',
            Borrower::ACTIVATION_APPROVED => 'Active'
        ];
    }
}
