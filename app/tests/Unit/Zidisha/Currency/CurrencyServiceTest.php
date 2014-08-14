<?php

namespace Unit\Zidisha\Currency;

use Carbon\Carbon;
use Zidisha\Currency\Currency;
use Zidisha\Currency\Money;

class CurrencyServiceTest extends \TestCase
{    
    /**
     * @var \Zidisha\Currency\CurrencyService
     */
    private $currencyService;

    public function setup()
    {
        parent::setup();
        $this->currencyService = $this->app->make('Zidisha\Currency\CurrencyService');
    }

    public function testGetExchangeRate()
    {
        // get exchange rate from one month ago
        $date = new Carbon();
        $date->subMonths(2);
        
        $rateTwoMonthsAgo = $this->currencyService->getExchangeRate(Currency::create(Currency::CODE_KES), $date);
        $this->assertEquals(Currency::CODE_KES, $rateTwoMonthsAgo->getCurrencyCode());
        $this->assertLessThan($date, $rateTwoMonthsAgo->getStartDate());
        $this->assertGreaterThan($date->copy()->subMonths(3), $rateTwoMonthsAgo->getStartDate());
        $this->assertGreaterThan($date, $rateTwoMonthsAgo->getEndDate());
        $this->assertGreaterThan($date->copy()->subMonths(3), $rateTwoMonthsAgo->getEndDate());

        // get current exchange rate
        $rateNow = $this->currencyService->getExchangeRate(Currency::create(Currency::CODE_KES));
        $this->assertEquals(Currency::CODE_KES, $rateNow->getCurrencyCode());
        $this->assertGreaterThan($date, $rateNow->getStartDate());
        $this->assertNull($rateNow->getEndDate());
    }
    
    public function testConvertCurrency()
    {
        $exchangeRate = new \Zidisha\Currency\ExchangeRate();
        $exchangeRate
            ->setRate(80)
            ->setCurrencyCode(Currency::CODE_KES);
        
        // convert to USD
        $money = Money::create('160', Currency::CODE_KES);
        $moneyUSD = Money::create('2.0', Currency::CODE_USD);
        $this->assertEquals($moneyUSD, $this->currencyService->convertToUSD($money, $exchangeRate));            
        
        // convert from USD
        $money = Money::create('240', Currency::CODE_KES);
        $moneyUSD = Money::create('3.0', Currency::CODE_USD);
        $this->assertEquals($money, $this->currencyService->convertFromUSD($moneyUSD, Currency::create(Currency::CODE_KES), $exchangeRate));
        
        $failed = false;
        try {
            $money = Money::create('160', Currency::CODE_XOF);
            $this->currencyService->convertToUSD($money, $exchangeRate);
        } catch (\Zidisha\Currency\Exception\InvalidCurrencyExchangeException $e) {
            $failed = true;
        }
        
        $this->assertTrue($failed);

        $failed = false;
        try {
            $this->currencyService->convertFromUSD($moneyUSD, Currency::create(Currency::CODE_XOF), $exchangeRate);
        } catch (\Zidisha\Currency\Exception\InvalidCurrencyExchangeException $e) {
            $failed = true;
        }

        $this->assertTrue($failed);
    }
}
