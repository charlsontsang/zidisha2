<?php

namespace Zidisha\Country;

use Zidisha\Country\Base\LanguageQuery as BaseLanguageQuery;
use Zidisha\Country\CountryQuery;
use Zidisha\Utility\Utility;


/**
 * Skeleton subclass for performing query and update operations on the 'languages' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class LanguageQuery extends BaseLanguageQuery
{

    public function filterBorrowerLanguages()
    {
        $languageCodes = CountryQuery::create()
            ->filterByBorrowerCountry(true)
            ->distinct()
            ->select(['language_code'])
            ->find();

        return $this->filterByLanguageCode($languageCodes->getData());
    }

    public function getLanguageCodeByIp()
    {
        $country = Utility::getCountryByIP();

        return $this
            ->useCountryQuery()
                ->filterById($country->getId())
                ->filterByBorrowerCountry(true)
            ->endUse()
            ->select('LanguageCode')
            ->findOne();
    }

} // LanguageQuery
