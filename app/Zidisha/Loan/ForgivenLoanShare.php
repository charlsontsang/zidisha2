<?php

namespace Zidisha\Loan;

use Zidisha\Currency\Money;
use Zidisha\Loan\Base\ForgivenLoanShare as BaseForgivenLoanShare;

class ForgivenLoanShare extends BaseForgivenLoanShare
{

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
