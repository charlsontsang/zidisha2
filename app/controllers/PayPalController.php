<?php

use PayPal\IPN\PPIPNMessage;
use Zidisha\Currency\Money;
use Zidisha\Payment\Paypal\PayPalService;

class PayPalController extends BaseController
{
    /**
     * @var Zidisha\Payment\Paypal\PayPalService
     */
    private $payPalService;

    public function __construct(PayPalService $payPalService)
    {
        $this->payPalService = $payPalService;
    }

    public function getStart()
    {
        $payment = new Zidisha\Payment\Payment();

        $payment->setAmount(Money::create(10));
        $payment->setTransactionFee(Money::create(5));
        $payment->setDonationAmount(Money::create(5));
        $payment->setTotalAmount(Money::create(20));
        $payment->save();

        return $this->payPalService->makePayment($payment);
    }

    public function getReturn()
    {
        $token = \Input::get('token');

        if (!$token) {
            \App::abort('Fatal Error.');
        }

        return $this->payPalService->getExpressCheckoutDetails($token);
    }

    public function getCancel()
    {
        $token = \Input::get('token');

        if (!$token) {
            \App::abort('Fatal Error.');
        }

        return $this->payPalService->getCancel($token);
    }

    public function postIpn()
    {
        $ipnMessage = new PPIPNMessage(null, \Config::get('paypal'));
        return $this->payPalService->processIpn($ipnMessage);
    }
}