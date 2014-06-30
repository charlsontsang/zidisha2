<?php
namespace Zidisha\Payment\Handler\GiftCard;


use Zidisha\Payment\Error\PaymentError;
use Zidisha\Payment\PaymentHandler;

class FailedHandler extends PaymentHandler
{
    public function redirect()
    {
        \Flash::error($this->paymentError->getMessage());
        return \Redirect::route('loan:index', ['id' => $this->payment->getLoanId()]);
    }
}
