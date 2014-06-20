<?php

namespace Zidisha\Utility;


use GeoIp2\Database\Reader;
use Zidisha\Country\CountryQuery;

class Utility {

   public static function getCountryCodeByIP(){
        $country['code'] = '';
        $country['name'] = '';
       $country['id'] = '';
        $ip = \Request::getClientIp();
        if(!empty($ip)) {
            $reader = new Reader( app_path() . '/storage/external/GeoLite2-Country.mmdb');
            $record = $reader->country('103.7.80.62');
            $country = array();
            $country['code'] = $record->country->isoCode;
            $country['name'] = $record->country->name;
            $dbCountry = CountryQuery::create()
                                ->findOneByCountryCode($country['code']);
            $country['id'] = $dbCountry->getId();
        }
        return $country;
    }

} 