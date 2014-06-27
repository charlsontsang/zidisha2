<?php
namespace Zidisha\Payment\Handler\UploadFund;


use Zidisha\Payment\Error\PaymentError;
use Zidisha\Payment\PaymentHandler;

class FailedHandler extends PaymentHandler
{
    public function redirect(PaymentError $paymentError)
    {
        \Flash::error($paymentError->getMessage());
        return \Redirect::route('lender:funds');
    }
} 