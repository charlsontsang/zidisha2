<?php

namespace Zidisha\Payment\Paypal;

use Zidisha\Currency\Money;
use Zidisha\Payment\Paypal\Base\PaypalTransaction as BasePaypalTransaction;

class PaypalTransaction extends BasePaypalTransaction
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

    public function getPaypalTransactionFee()
    {
        return Money::create(parent::getPaypalTransactionFee(), 'USD');
    }

    public function setPaypalTransactionFee($money)
    {
        return parent::setPaypalTransactionFee($money->getAmount());
    }
}
