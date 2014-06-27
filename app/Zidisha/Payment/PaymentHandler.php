<?php
namespace Zidisha\Payment;

class PaymentHandler
{

    /**
     * @var Payment
     */
    protected $payment;

    public function process() {
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
    }
}
