<?php

use Zidisha\Currency\Currency;
use Zidisha\Currency\Money;

class CurrencyServiceCest
{    
    /**
     * @var Zidisha\Currency\CurrencyService
     */
    private $currencyService;

    public function _before(UnitTester $I)
    {
        $this->currencyService = $I->grabService('Zidisha\Currency\CurrencyService');
    }

    public function _after(UnitTester $I)
    {
    }

    public function testGetExchangeRate(UnitTester $I)
    {
        // get exchange rate from one month ago
        $date = new \DateTime();
        $date->modify('-1 month');
        $rateMonthAgo = $this->currencyService->getExchangeRate(Currency::valueOf(Currency::CODE_KES), $date);
        verify($rateMonthAgo->getCurrencyCode())->equals(Currency::CODE_KES);
        verify($rateMonthAgo->getRate())->equals('75'); 

        // get current exchange rate
        $rateNow = $this->currencyService->getExchangeRate(Currency::valueOf(Currency::CODE_KES));
        verify($rateNow->getCurrencyCode())->equals(Currency::CODE_KES);
        verify($rateNow->getRate())->equals('80');
    }
    
    public function testConvertCurrency(UnitTester $I)
    {
        // get current exchange rate
        $money = Money::valueOf('160', Currency::valueOf(Currency::CODE_KES));
        $moneyUSD = Money::valueOf('2.0', Currency::valueOf(Currency::CODE_USD));
        verify($this->currencyService->convertToUSD($money))->equals($moneyUSD);            
        
        // get current exchange rate
        $money = Money::valueOf('240', Currency::valueOf(Currency::CODE_KES));
        $moneyUSD = Money::valueOf('3.0', Currency::valueOf(Currency::CODE_USD));
        verify($this->currencyService->convertFromUSD($moneyUSD, Currency::valueOf(Currency::CODE_KES)))->equals($money);
    }
}
