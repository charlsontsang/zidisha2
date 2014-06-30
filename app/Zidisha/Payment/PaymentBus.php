<?php
namespace Zidisha\Payment;


use Zidisha\Payment\Error\PaymentError;

class PaymentBus
{
    /**
     * @param Payment $payment
     * @param PaymentError $paymentError
     * @return mixed
     */
    public function getFailedHandler(Payment $payment, PaymentError $paymentError)
    {
        return $this->getHandler($payment, 'Failed')->setPaymentError($paymentError);
    }

    /**
     * @param Payment $payment
     * @return mixed
     */
    public function getCompletedHandler(Payment $payment)
    {
        return $this->getHandler($payment, 'Completed');
    }

    /**
     * @param Payment $payment
     * @param PaymentError $paymentError
     * @return mixed
     */
    public function getPendingHandler(Payment $payment, PaymentError $paymentError)
    {
        return $this->getHandler($payment, 'Pending')->setPaymentError($paymentError);
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
