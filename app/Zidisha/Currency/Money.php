<?php

namespace Zidisha\Currency;

class Money extends \SupremeNewMedia\Finance\Core\Money {

    public static function create($amount, $currency = Currency::CODE_USD)
    {
        if (is_string($currency)) {
            $currency = Currency::valueOf($currency);
        }
        
        return parent::valueOf($amount, $currency);
    }

    /**
     * Compares this moneys amount with the given ones and returns 0 if they are equal, 1 if this amount is larger than
     * the given ones, -1 otherwise. This method explicitly disregards the Currency!
     *
     * @param Money $money the money to compare with
     *
     * @return integer
     */
    public function compare(\SupremeNewMedia\Finance\Core\Money $money)
    {
        return bccomp($this->getAmount(), $money->getAmount(), 2);
    }
    
    public function lessThan(Money $money)
    {
        return $this->compare($money) == -1;
    }

    public function greaterThan(Money $money)
    {
        return $this->compare($money) == 1;
    }

    public function max(Money $money)
    {
        return $this->lessThan($money) ? $money : $this;
    }

    public function min(Money $money)
    {
        return $this->lessThan($money) ? $this : $money;
    }

    /**
     * Is the current amount zero?
     *
     * @return boolean
     */
    public function isAmountZero()
    {
        return (0 == bccomp('0', $this->getAmount(), 2));
    }
}
