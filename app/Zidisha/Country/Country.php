<?php

namespace Zidisha\Country;

use Zidisha\Country\Base\Country as BaseCountry;
use Zidisha\Currency\Currency;
use Zidisha\Currency\Money;


class Country extends BaseCountry
{
    public function setName($name)
    {
        parent::setName($name);
        $this->setSlug(str_replace(' ', '-', strtolower($name)));

        return $this;
    }

    public function getCurrency()
    {
        return Currency::create($this->getCurrencyCode());
    }

    public function isFacebookRequired()
    {
        return ! $this->getCountryCode() == 'BF';
    }

    /**
     * @return Money
     */
    public function getRegistrationFee()
    {
        return Money::create(parent::getRegistrationFee(), $this->getCurrencyCode());
    }

    /**
     * @param Money $money
     * @return $this|Country
     */
    public function setRegistrationFee($money)
    {
        return parent::setRegistrationFee($money->getAmount());
    }
}
