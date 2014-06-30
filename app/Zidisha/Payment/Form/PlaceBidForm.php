<?php
namespace Zidisha\Payment\Form;


use Zidisha\Currency\Money;
use Zidisha\Payment\BidPayment;

class PlaceBidForm extends AbstractPaymentForm
{

    public function getRules($data)
    {
        return [
            'amount' => '',
            'loanId' => 'assert',
            'interestRate' => '',
        ] + parent::getRules($data);
    }

    public function getPayment()
    {

        if (!\Auth::user()) {
            \App::abort(404, 'Fatal Error');
        }

        $lender = \Auth::user()->getLender();

        $data = $this->getData();

        $placeBidPayment = new BidPayment();
        $placeBidPayment
            ->setCreditAmount(Money::create($data['creditAmount']))
            ->setDonationAmount(Money::create($data['donationAmount']))
            ->setTransactionFee(Money::create($data['transactionFee']))
            ->setTotalAmount(Money::create($data['totalAmount']))
            ->setLoanId($data['loanId'])
            ->setInterestRate($data['interestRate'])
            ->setBidAmount(Money::create($data['amount']))
            ->setLender($lender);

        return $placeBidPayment;
    }

    public function getRates()
    {
        $keys = range(0, 15);
        $values = array_map(
            function ($a) {
                return "$a%";
            },
            $keys
        );

        return array_combine($keys, $values);
    }
}