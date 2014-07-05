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
        $rateMonthAgo = $this->currencyService->getExchangeRate(Currency::create(Currency::CODE_KES), $date);
        verify($rateMonthAgo->getCurrencyCode())->equals(Currency::CODE_KES);
        verify($rateMonthAgo->getRate())->equals('75'); 

        // get current exchange rate
        $rateNow = $this->currencyService->getExchangeRate(Currency::create(Currency::CODE_KES));
        verify($rateNow->getCurrencyCode())->equals(Currency::CODE_KES);
        verify($rateNow->getRate())->equals('80');
    }
    
    public function testConvertCurrency(UnitTester $I)
    {
        $exchangeRate = new \Zidisha\Currency\ExchangeRate();
        $exchangeRate
            ->setRate(80)
            ->setCurrencyCode(Currency::CODE_KES);
        
        // convert to USD
        $money = Money::create('160', Currency::CODE_KES);
        $moneyUSD = Money::create('2.0', Currency::CODE_USD);
        verify($this->currencyService->convertToUSD($money, $exchangeRate))->equals($moneyUSD);            
        
        // convert from USD
        $money = Money::create('240', Currency::CODE_KES);
        $moneyUSD = Money::create('3.0', Currency::CODE_USD);
        verify($this->currencyService->convertFromUSD($moneyUSD, Currency::create(Currency::CODE_KES), $exchangeRate))->equals($money);
        
        $failed = false;
        try {
            $money = Money::create('160', Currency::CODE_XOF);
            $this->currencyService->convertToUSD($money, $exchangeRate);
        } catch (\Zidisha\Currency\Exception\InvalidCurrencyExchangeException $e) {
            $failed = true;
        }
        
        verify($failed)->true();

        $failed = false;
        try {
            $this->currencyService->convertFromUSD($moneyUSD, Currency::create(Currency::CODE_XOF), $exchangeRate);
        } catch (\Zidisha\Currency\Exception\InvalidCurrencyExchangeException $e) {
            $failed = true;
        }

        verify($failed)->true();
    }
}
