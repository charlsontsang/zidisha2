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
        $countryCode = 'KE';

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
        $currency = $country->getCurrency();

        $firstLoanValue = Money::create(Setting::get('loan.firstLoanValue'), 'USD');
        $nextLoanValue = Money::create(Setting::get('loan.nextLoanValue'), 'USD');
        $secondLoanPercentage = Setting::get('loan.secondLoanPercentage');
        $nextLoanPercentage = Setting::get('loan.nextLoanPercentage');
        
        /* TO DO: Comment out these hard-coded values once Setting::get is defined */
        $firstLoanValue = Money::create('50', 'USD');
        $nextLoanValue = Money::create('10000', 'USD');
        $secondLoanPercentage = 300;
        $nextLoanPercentage = 150;

        $params['firstLoanVal'] = Converter::fromUSD($firstLoanValue, $currency, $exchangeRate);
        $params['nxtLoanvalue'] = '';
        $value = $firstLoanValue;

        for ($i = 2; $i < 12; $i++) {
            if ($value->lessThanOrEqual(Money::create(200, 'USD'))) {
                $value = $value->multiply($secondLoanPercentage)->divide(100);
                $val= Converter::fromUSD($value, $currency, $exchangeRate);
                $params['nxtLoanvalue'] .= "<br/>".$i.". ".' '.$val;
            } else {
                $value = $value->multiply($nextLoanPercentage)->divide(100);
                $localValue = Converter::fromUSD($value, $currency, $exchangeRate);
                $params['nxtLoanvalue'] .="<br/>".$i.". ".' '.$localValue;
            }
            if (!$value->lessThanOrEqual($nextLoanValue)) {
                $value = $nextLoanValue;
                $val= Converter::fromUSD($value, $currency, $exchangeRate);
                $params['nxtLoanvalue'] .= "<br/>".$i.". ".' '.$val;
            }
        }
        
        return View::make('borrower-home', compact ('params'));
    }

}
