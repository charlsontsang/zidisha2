<?php

namespace Zidisha\Currency;

use Zidisha\Currency\Base\ExchangeRate as BaseExchangeRate;

class ExchangeRate extends BaseExchangeRate
{

    public function getCurrency()
    {
        return Currency::create($this->getCurrencyCode());
    }

}
