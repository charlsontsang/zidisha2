<?php

namespace Zidisha\Payment\Stripe;

use Zidisha\Currency\Money;
use Zidisha\Payment\Stripe\Base\StripeTransaction as BaseStripeTransaction;

class StripeTransaction extends BaseStripeTransaction
{
    public function getTotalAmount()
    {
        return Money::create(parent::getTotalAmount(), 'USD');
    }

    public function setTotalAmount($money)
    {
        return parent::setTotalAmount($money->getAmount());
    }

    public function getAmount()
    {
        return Money::create(parent::getAmount(), 'USD');
    }

    public function setAmount($money)
    {
        return parent::setAmount($money->getAmount());
    }

    public function getDonationAmount()
    {
        return Money::create(parent::getDonationAmount(), 'USD');
    }

    public function setDonationAmount($money)
    {
        return parent::setDonationAmount($money->getAmount());
    }

    public function getTransactionFee()
    {
        return Money::create(parent::getTransactionFee(), 'USD');
    }

    public function setTransactionFee($money)
    {
        return parent::setTransactionFee($money->getAmount());
    }
}
