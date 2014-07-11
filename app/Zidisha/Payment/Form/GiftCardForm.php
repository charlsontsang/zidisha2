<?php

namespace Zidisha\Payment\Form;


use Zidisha\Currency\Money;
use Zidisha\Payment\GiftCardPayment;

class GiftCardForm extends AbstractPaymentForm
{

    public function getPayment()
    {

        if (!\Auth::user()) {
            \App::abort(404, 'Fatal Error');
        }

        $lender = \Auth::user()->getLender();

        $data = $this->getData();

        $giftCardPayment = new GiftCardPayment();
        $giftCardPayment
            ->setAmount(Money::create($data['amount']))
            ->setDonationAmount(Money::create($data['donationAmount']))
            ->setDonationCreditAmount(Money::create($data['donationCreditAmount']))
            ->setTransactionFee(Money::create($data['transactionFee']))
            ->setTotalAmount(Money::create($data['totalAmount']))
            ->setInterestRate($data['interestRate'])
            ->setLender($lender);

        return $giftCardPayment;
    }
} 