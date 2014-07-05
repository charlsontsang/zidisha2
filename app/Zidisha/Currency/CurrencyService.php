<?php
namespace Zidisha\Currency;

use Zidisha\Country\CountryQuery;
use Zidisha\Currency\Exception\InvalidCurrencyExchangeException;

class CurrencyService
{
    public function convertToUSD(Money $money, ExchangeRate $exchangeRate)
    {
        if ($exchangeRate->getCurrency() != $money->getCurrency()) {
            throw new InvalidCurrencyExchangeException();
        }
        
        $rate = $exchangeRate->getRate();
        $amountInUSD = $money->divide($rate);

        return Money::create($amountInUSD->getAmount(), Currency::CODE_USD);
    }

    public function convertFromUSD(Money $money, Currency $currency, ExchangeRate $exchangeRate)
    {
        if ($exchangeRate->getCurrency() != $currency) {
            throw new InvalidCurrencyExchangeException();
        }
        
        $rate = $exchangeRate->getRate();
        $amount = $money->multiply($rate);

        return Money::create($amount->getAmount(), $currency);
    }

    public function getExchangeRate(Currency $currency, \DateTime $date = null)
    {
        $date = $date ?: new \DateTime();

        return ExchangeRateQuery::create()
            ->filterByCurrencyCode($currency->getCode())
            ->condition('start', 'ExchangeRate.StartDate <= ?', $date)
            ->condition('endDate', 'ExchangeRate.EndDate > ?', $date)
            ->condition('endNull', 'ExchangeRate.EndDate IS NULL')
            ->combine(['endDate', 'endNull'], 'or', 'end')
            ->where(['start', 'end'], 'and')
            ->findOne();
    }

    public function getExchangeRatesForCountry($countrySlug)
    {
        if ($countrySlug) {
            $country = CountryQuery::create()
                ->filterBySlug($countrySlug)
                ->findOne();
        } else {
            $country = CountryQuery::create()
                ->filterByBorrowerCountry(true)
                ->orderByName()
                ->findOne();
        }

        $rates = ExchangeRateQuery::create()
            ->filterByCurrencyCode($country->getCurrencyCode())
            ->orderByStartDate('desc');

        return $rates;
    }

    public function updateExchangeRateForCountry($data)
    {
        $country = CountryQuery::create()
            ->filterBySlug($data['countrySlug'])
            ->findOne();
        $currencyCode = $country->getCurrency()->getCode();

        $lastRate = ExchangeRateQuery::create()
            ->filterByCurrencyCode($currencyCode)
            ->filterByEndDate(null)
            ->findone();

        if ($lastRate) {
            $lastRate->setEndDate(new \DateTime());
            $lastRate->save();
        }

        $newRate = new ExchangeRate();
        $newRate->setStartDate(new \DateTime())
            ->setCurrencyCode($currencyCode)
            ->setRate($data['newRate']);

        $newRate->save();

        return $newRate;
    }
}
