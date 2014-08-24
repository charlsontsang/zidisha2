<?php

namespace Zidisha\Loan;

use Zidisha\Currency\Money;
use Zidisha\Loan\Base\ForgivenessLoanShare as BaseForgivenessLoanShare;

class ForgivenessLoanShare extends BaseForgivenessLoanShare
{

    /**
     * @param Money $money
     * @return $this|ForgivenessLoanShare
     */
    public function setAmount($money)
    {
        return parent::setAmount($money->getAmount());
    }

    /**
     * @return Money
     */
    public function getAmount()
    {
        return Money::create(parent::getAmount(), 'USD');
    }

    /**
     * @param Money $money
     * @return $this|ForgivenessLoanShare
     */
    public function setUsdAmount($money)
    {
        return parent::setUsdAmount($money->getAmount());
    }

    /**
     * @return Money
     */
    public function getUsdAmount()
    {
        return Money::create(parent::getUsdAmount(), 'USD');
    }
}
