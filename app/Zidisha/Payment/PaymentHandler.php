<?php
namespace Zidisha\Payment;

use Zidisha\Payment\Error\PaymentError;

class PaymentHandler
{

    /**
     * @var Payment
     */
    protected $payment;

    /**
     * @var PaymentError
     */
    protected $paymentError;

    public function process()
    {
        return $this;
    }

    public function redirect()
    {
        return \Redirect::route('home');
    }

    /**
     * @param $payment
     * @return $this
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * @param PaymentError $paymentError
     * @return $this
     */
    public function setPaymentError(PaymentError $paymentError)
    {
        $this->paymentError = $paymentError;

        return $this;
    }

}
