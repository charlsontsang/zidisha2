<?php

use PayPal\IPN\PPIPNMessage;
use Zidisha\Vendor\Paypal\PaypalService;

class PayPalController extends BaseController
{
    /**
     * @var Zidisha\Vendor\Paypal\PaypalService
     */
    private $paypalService;

    public function __construct(PaypalService $paypalService)
    {

        $this->paypalService = $paypalService;
    }

    public function setExpressToken()
    {
        $payments = [
            'amount' => '12',
            'donationAmount' => '5',
            'paypalTransactionFee' => '5',
            'totalAmount' => '22',
            'type' => 'fund',
            'userId' => '32'
        ];

        return $this->paypalService->makePayment($payments);
    }

    public function process()
    {
        return $this->paypalService->getExpressCheckoutDetails();
    }

    public function cancel()
    {
        dd(\Input::all());
    }

    public function notification()
    {
        $ipnMessage = new PPIPNMessage(null, \Config::get('paypal'));
        foreach($ipnMessage->getRawData() as $key => $value) {
            \App::log("IPN: $key => $value");
        }

//        if($ipnMessage->validate()) {
//            error_log("Success: Got valid IPN data");
//        } else {
//            error_log("Error: Got invalid IPN data");
//        }
    }
}