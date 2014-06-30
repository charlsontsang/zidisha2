<?php

namespace Zidisha\Payment;

use Zidisha\Currency\Money;
use Zidisha\Payment\Base\Payment as BasePayment;

class Payment extends BasePayment
{
    public function getCreditAmount()
    {
        return Money::create(parent::getCreditAmount(), 'USD');
    }

    public function setCreditAmount($money)
    {
        return parent::setCreditAmount($money->getAmount());
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

    public function getTotalAmount()
    {
        return Money::create(parent::getTotalAmount(), 'USD');
    }

    public function setTotalAmount($money)
    {
        return parent::setTotalAmount($money->getAmount());
    }

    public function getBidAmount()
    {
        return Money::create(parent::getBidAmount(), 'USD');
    }

    public function setBidAmount($money)
    {
        return parent::setBidAmount($money->getAmount());
    }
}
