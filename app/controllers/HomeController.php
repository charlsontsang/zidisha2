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
        $secondaryCaption = 'and join the global <strong>person-to-person</strong> microlending movement.';
        $buttonText = 'Browse Projects';

		return View::make('lender-home', compact('secondaryCaption','buttonText'));
	}

    private function getBorrowerHome()
    {
        return View::make('borrower-home');
    }

}
