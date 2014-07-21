<?php

use Zidisha\Country\CountryQuery;
use Zidisha\User\User;
use Zidisha\Utility\Utility;

class HomeController extends BaseController {


    public function getHome()
    {
        $countryCode = Utility::getCountryCodeByIP();
        $country = CountryQuery::create()
            ->findOneByCountryCode($countryCode);
        if($country && $country->isBorrowerCountry()) {
            return $this->getBorrowerHome();
        }
        return $this->getLenderHome();
    }
    
	public function getLenderHome()
	{
		return View::make('lender-home');
	}

    private function getBorrowerHome()
    {
        return View::make('borrower-home');
    }

}
