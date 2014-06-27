<?php
namespace Zidisha\Payment;


class PaymentBus
{
    /**
     * @param Payment $payment
     * @return PaymentHandler
     */
    public function getFailedHandler(Payment $payment)
    {
        $paymentHandler = \App::make(get_class($payment) . 'FailedHandler');
        $paymentHandler->setPayment($payment);
        return $paymentHandler;
    }

    /**
     * @param Payment $payment
     * @return PaymentHandler
     */
    public function getCompletedHandler(Payment $payment)
    {
        $paymentHandler = \App::make(get_class($payment) . 'CompletedHandler');
        $paymentHandler->setPayment($payment);
        return $paymentHandler;
    }

    /**
     * @param Payment $payment
     * @return PaymentHandler
     */
    public function getPendingHandler(Payment $payment)
    {
        $paymentHandler =  \App::make(get_class($payment) . 'PendingHandler');
        $paymentHandler->setPayment($payment);
        return $paymentHandler;
    }
}
