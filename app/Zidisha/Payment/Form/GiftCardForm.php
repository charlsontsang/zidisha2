<?php

namespace Zidisha\Payment\Form;


use Zidisha\Currency\Money;
use Zidisha\Lender\GiftCardService;
use Zidisha\Payment\GiftCardPayment;

class GiftCardForm extends AbstractPaymentForm
{

    private $giftCardService;

    public function __construct(GiftCardService $giftCardService){

        $this->giftCardService = $giftCardService;
    }

    public function getPayment()
    {
        if (!\Auth::user()) {
            \App::abort(404, 'Fatal Error');
        }

        $lender = \Auth::user()->getLender();

        $data = $this->getData();
        $giftCardData = \Session::get('giftCard');
        $giftCard = $this->giftCardService->addGiftCard($lender, $giftCardData);

        $giftCardPayment = new GiftCardPayment();
        $giftCardPayment
            ->setCreditAmount(Money::create($data['creditAmount']))
            ->setAmount(Money::create($data['amount']))
            ->setDonationAmount(Money::create($data['donationAmount']))
            ->setDonationCreditAmount(Money::create($data['donationCreditAmount']))
            ->setTransactionFee(Money::create($data['transactionFee']))
            ->setTotalAmount(Money::create($data['totalAmount']))
            ->setGiftCardTransaction($giftCard->getGiftCardTransaction())
            ->setLender($lender);

        return $giftCardPayment;
    }
} 