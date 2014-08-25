<?php


namespace Zidisha\Admin\Form;


use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Country\CountryQuery;
use Zidisha\Form\AbstractForm;

class FilterBorrowers extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'country' => '',
            'search'  => '',
            'status'  => ''
        ];
    }

    public function getDefaultData()
    {

        return [
            'country' => '',
            'search'  => '',
            'status'  => ''
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
            'all'                           => 'All',
            Borrower::ACTIVATION_INCOMPLETE => 'Pending Submission',
            Borrower::ACTIVATION_PENDING    => 'Pending Activation',
            Borrower::ACTIVATION_DECLINED   => 'Declined',
            Borrower::ACTIVATION_APPROVED   => 'Active'
        ];
    }

    public function getQuery($query = null)
    {
        $countryId = \Request::query('country');
        $status = \Request::query('status');
        $search = \Request::query('search');

        if (!$query) {
            $query = BorrowerQuery::create();
        }

        if ($countryId != 'all_countries' && $countryId) {
            $query->filterByCountryId($countryId);
        }

        if ($status != 'all' && $status) {
            $query->filterByActivationStatus($status);
        }

        if ($search) {
            $query
                ->where("borrowers.last_name  || ' ' || borrowers.first_name LIKE ?", '%' . $search . '%')
                ->_or()
                ->where("borrowers.first_name  || ' ' || borrowers.last_name LIKE ?", '%' . $search . '%')
                ->_or()
                ->useProfileQuery()
                ->filterByPhoneNumber('%' . $search . '%', Criteria::LIKE)
                ->endUse()
                ->_or()
                ->useUserQuery()
                ->filterByEmail('%' . $search . '%', Criteria::LIKE)
                ->endUse();
        }
        
        return $query;
    }
    
    public function getPaginatorParams()
    {
        return [
            'country' => \Request::query('country'),
            'search'  => \Request::query('search'),
            'status'  => \Request::query('status')
        ];
    }
    
    public function isFiltering()
    {
        return count(array_filter($this->getPaginatorParams())) > 0;
    }
}
