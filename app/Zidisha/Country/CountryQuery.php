<?php

namespace Zidisha\Country;

use Zidisha\Country\Base\CountryQuery as BaseCountryQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'countries' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class CountryQuery extends BaseCountryQuery
{

    public function getOneByCountryCode($countryCode)
    {
        return $this
            ->filterByCountryCode($countryCode)
            ->findOne();
    }
} // CountryQuery
