<?php
namespace Zidisha\Payment\Bid;

use Zidisha\Payment\Error\PaymentError;
use Zidisha\Payment\PaymentHandler;

class BidPaymentFailedHandler implements PaymentHandler
{

    public function process()
    {
        return $this;
    }

    public function redirect(PaymentError $paymentError)
    {
        \Flash::error($paymentError->getMessage());
        return \Redirect::route('home');
    }
}