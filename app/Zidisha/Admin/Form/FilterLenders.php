<?php


namespace Zidisha\Admin\Form;


use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Country\CountryQuery;
use Zidisha\Form\AbstractForm;
use Zidisha\Lender\LenderQuery;

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

    public function getQuery($query = null)
    {
        $countryId = \Request::query('country') ? : null;
        $search = \Request::query('search') ? : null;

        if (!$query) {
            $query = LenderQuery::create();
        }

        if ($countryId != 'all_countries' && $countryId) {
            $query->filterByCountryId($countryId);
        }

        if ($search) {
            $query
                ->where("lenders.last_name  || ' ' || lenders.first_name LIKE ?", '%' . $search . '%')
                ->_or()
                ->where("lenders.first_name  || ' ' || lenders.last_name LIKE ?", '%' . $search . '%')
                ->_or()
                ->useProfileQuery()
                ->filterByCity('%' . $search . '%', Criteria::LIKE)
                ->endUse()
                ->_or()
                ->useUserQuery()
                ->filterByEmail('%' . $search . '%', Criteria::LIKE)
                ->_or()
                ->filterByUsername('%' . $search . '%', Criteria::LIKE)
                ->endUse();
        }

        return $query;
    }
}
