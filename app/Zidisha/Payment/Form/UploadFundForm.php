<?php
namespace Zidisha\Payment\Form;


use Zidisha\Currency\Money;
use Zidisha\Payment\UploadFundPayment;

class UploadFundForm  extends AbstractPaymentForm{

    public function getPayment()
    {
        if(!\Auth::getUser()){
            \App::abort('Fatal Error');
        }

        $lender = \Auth::getUser()->getLender();

        $data = $this->getData();
        $uploadPayment = new UploadFundPayment();
        $uploadPayment
            ->setAmount(Money::create($data['amount']))
            ->setDonationAmount(Money::create($data['donationAmount']))
            ->setTransactionFee(Money::create($data['transactionFee']))
            ->setTotalAmount(Money::create($data['totalAmount']))
            ->setLender($lender);

        return $uploadPayment;
    }
}
