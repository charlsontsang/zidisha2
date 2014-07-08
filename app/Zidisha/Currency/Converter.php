<?php

namespace Zidisha\Currency;


class Converter {

    /**
     * @var \Zidisha\Currency\CurrencyService
     */
    protected static $currencyService;
    
    protected static function getCurrencyService()
    {
        if (static::$currencyService === null) {
            static::$currencyService = \App::make('Zidisha\Currency\CurrencyService');
        }
        
        return static::$currencyService;
    }
    
    public static function toUSD(Money $money, ExchangeRate $exchangeRate)
    {
        return static::getCurrencyService()->convertToUSD($money, $exchangeRate);
    }

    public static function fromUSD(Money $money, Currency $currency, ExchangeRate $exchangeRate)
    {
        return static::getCurrencyService()->convertFromUSD($money, $currency, $exchangeRate);
    }
    
}
