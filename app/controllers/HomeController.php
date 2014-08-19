<?php

use Zidisha\Country\CountryQuery;
use Zidisha\User\User;
use Zidisha\Utility\Utility;
use Zidisha\Admin\Setting;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Currency\Money;
use Zidisha\Currency\Converter;

class HomeController extends BaseController {


    public function getHome()
    {
        $countryCode = Utility::getCountryCodeByIP();

        $country = CountryQuery::create()
            ->findOneByCountryCode($countryCode);
        if($country && $country->isBorrowerCountry()) {
            return $this->getBorrowerHome($country);
        }
        return $this->getLenderHome();
    }
    
    public function getLenderHome()
    {
        $secondaryCaption = 'and join the global <strong>person-to-person</strong> microlending movement.';
        $buttonText = 'Browse Projects';

        return View::make('lender-home', compact('secondaryCaption','buttonText'));
    }

    private function getBorrowerHome($country)
    {
        $exchangeRate = ExchangeRateQuery::create()
            ->findCurrent($country->getCurrency());

        $firstLoanValue = Money::create(Setting::get('loan.firstLoanValue'), $country->getCurrencyCode(), $exchangeRate);
        $firstLoanInvited = Money::create((Setting::get('loan.firstLoanValue')+50), $country->getCurrencyCode(), $exchangeRate);
        $secondLoanValue = $firstLoanValue * (Setting::get('loan.secondLoanPercentage')/100);
        $thirdLoanValue = $secondLoanValue * (Setting::get('loan.secondLoanPercentage')/100);
        $fourthLoanValue = $thirdLoanValue * (Setting::get('loan.nextLoanPercentage')/100);
        $fifthLoanValue = $fourthLoanValue * (Setting::get('loan.nextLoanPercentage')/100);
        $sixthLoanValue = $fifthLoanValue * (Setting::get('loan.nextLoanPercentage')/100);
        $seventhLoanValue = $sixthLoanValue * (Setting::get('loan.nextLoanPercentage')/100);
        $eighthLoanValue = $seventhLoanValue * (Setting::get('loan.nextLoanPercentage')/100);
        $ninethLoanValue = $eighthLoanValue * (Setting::get('loan.nextLoanPercentage')/100);
        $tenthLoanValue = $ninethLoanValue * (Setting::get('loan.nextLoanPercentage')/100);
        $nextLoanValue = Money::create(Setting::get('loan.nextLoanValue'), $country->getCurrencyCode(), $exchangeRate);

        return View::make('borrower-home', compact ('firstLoanValue', 'firstLoanInvited', 'secondLoanValue', 'thirdLoanValue', 'fourthLoanValue', 'fifthLoanValue', 'sixthLoanValue', 'seventhLoanValue', 'eighthLoanValue', 'ninethLoanValue', 'tenthLoanValue', 'nextLoanValue'));
    }

}
