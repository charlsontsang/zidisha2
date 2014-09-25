<?php
namespace Zidisha\Payment\Handler\UploadFund;


use Zidisha\Payment\PaymentHandler;

class FailedHandler extends PaymentHandler
{
    public function redirect()
    {
        \Flash::error($this->paymentError->getMessage());
        return \Redirect::route('lender:funds');
    }
} 