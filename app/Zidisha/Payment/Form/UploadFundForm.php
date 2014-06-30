<?php
namespace Zidisha\Payment\Form;


use Zidisha\Currency\Money;
use Zidisha\Payment\UploadFundPayment;

class UploadFundForm  extends AbstractPaymentForm{

    public function getPayment()
    {
        if(!\Auth::user()){
            \App::abort(404, 'Fatal Error');
        }

        $lender = \Auth::user()->getLender();

        $data = $this->getData();
        $uploadPayment = new UploadFundPayment();
        $uploadPayment
            ->setCreditAmount(Money::create($data['creditAmount']))
            ->setDonationAmount(Money::create($data['donationAmount']))
            ->setTransactionFee(Money::create($data['transactionFee']))
            ->setTotalAmount(Money::create($data['totalAmount']))
            ->setLender($lender);

        return $uploadPayment;
    }
}
