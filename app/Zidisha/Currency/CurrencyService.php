<?php
namespace Zidisha\Currency;

class CurrencyService
{
    public function convertToUSD(Money $money, \DateTime $date = null)
    {
        $exchangeRate = $this->getExchangeRate($money->getCurrency(), $date);
        $rate = $exchangeRate->getRate();

        $amountInUSD = $money->divide($rate);

        return Money::valueOf($amountInUSD->getAmount(), Currency::valueOf(Currency::CODE_USD));
    }

    public function convertFromUSD(Money $money, Currency $currency, \DateTime $date = null)
    {
        $exchangeRate = $this->getExchangeRate($currency, $date);
        $rate = $exchangeRate->getRate();

        $amount = $money->multiply($rate);

        return Money::valueOf($amount->getAmount(), $currency);
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
}
