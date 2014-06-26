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
        return App::make(get_class($payment) . 'FailedHandler');
    }

    /**
     * @param Payment $payment
     * @return PaymentHandler
     */
    public function getCompletedHandler(Payment $payment)
    {
        return App::make(get_class($payment) . 'CompletedHandler');
    }

    /**
     * @param Payment $payment
     * @return PaymentHandler
     */
    public function getPendingHandler(Payment $payment)
    {
        return App::make(get_class($payment) . 'PendingHandler');
    }
}
