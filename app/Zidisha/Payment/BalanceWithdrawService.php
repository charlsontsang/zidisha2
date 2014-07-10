<?php

namespace Zidisha\Payment;


class BalanceWithdrawService extends PaymentService
{
    private $paymentBus;

    public function __construct(PaymentBus $paymentBus)
    {
        $this->paymentBus = $paymentBus;
    }

    public function makePayment(Payment $payment, $data = [])
    {
        return $this->paymentBus->getCompletedHandler($payment)->process()->redirect();
    }
}
