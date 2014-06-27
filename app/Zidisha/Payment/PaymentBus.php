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
        return $this->getHandler($payment, 'Failed');
    }

    /**
     * @param Payment $payment
     * @return PaymentHandler
     */
    public function getCompletedHandler(Payment $payment)
    {
        return $this->getHandler($payment, 'Completed');
    }

    /**
     * @param Payment $payment
     * @return PaymentHandler
     */
    public function getPendingHandler(Payment $payment)
    {
        return $this->getHandler($payment, 'Pending');
    }

    /**
     * @param Payment $payment
     * @param $status
     * @return mixed
     */
    protected function getHandler(Payment $payment, $status)
    {
        $class = get_class($payment);
        $parts = explode('\\', $class);
        $name = array_pop($parts);
        $type = substr($name, 0, -7);
        $parts[] = 'Handler';
        $parts[] = $type;
        $parts[] = $status . 'Handler';
        $handler = implode('\\', $parts);
        $paymentHandler = \App::make($handler);
        $paymentHandler->setPayment($payment);

        return $paymentHandler;
    }
}
