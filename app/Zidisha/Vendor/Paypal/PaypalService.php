<?php
namespace Zidisha\Vendor\Paypal;

use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\EBLBaseComponents\DoExpressCheckoutPaymentRequestDetailsType;
use PayPal\EBLBaseComponents\PaymentDetailsItemType;
use PayPal\EBLBaseComponents\PaymentDetailsType;
use PayPal\EBLBaseComponents\SetExpressCheckoutRequestDetailsType;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentReq;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentRequestType;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsReq;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsRequestType;
use PayPal\PayPalAPI\SetExpressCheckoutReq;
use PayPal\PayPalAPI\SetExpressCheckoutRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;

class PaypalService
{

    /**
     * @var \PayPal\Service\PayPalAPIInterfaceServiceService
     */
    private $paypalService;

    public function __construct()
    {
        $this->paypalService = new PayPalAPIInterfaceServiceService(\Config::get('paypal'));
    }

    public function makePayment($payment)
    {
        $requiredKeys = [
            'amount',
            'donationAmount',
            'paypalTransactionFee',
            'totalAmount',
            'type', // gift or fund
            'userId'
        ];

        if (array_diff($requiredKeys, array_keys($payment))) {
            throw new \Exception();
        }

        $amount = $payment['totalAmount'];
        $paypalCustomVariable = md5($payment['userId'] . time());

        $paypalTransaction = new PaypalTransaction();
        $paypalTransaction->setAmount($payment['amount']);
        $paypalTransaction->setDonationAmount($payment['donationAmount']);
        $paypalTransaction->setPaypalTransactionFee($payment['paypalTransactionFee']);
        $paypalTransaction->setTotalAmount($payment['totalAmount']);
        $paypalTransaction->setStatus('START');
        $paypalTransaction->setCustom($paypalCustomVariable);
        $paypalTransaction->setTransactionType($payment['type']);
        $paypalTransaction->save();

        $paymentDetail = new PaymentDetailsType();

        // Make a new item for user purchase
        $itemDetail = new PaymentDetailsItemType();
        $itemDetail->Name = 'Lend To Zidisha';
        $itemDetail->Amount = $amount;
        $itemDetail->Quantity = '1';
        $itemDetail->ItemCategory = 'Digital';

        //Add this item to payment and set the order amount
        $paymentDetail->PaymentDetailsItem = $itemDetail;
        $paymentDetail->OrderTotal = new BasicAmountType('USD', $amount);
        $paymentDetail->NotifyURL = \Config::get('paypal.notification_url');


        $setECReqDetails = new SetExpressCheckoutRequestDetailsType();
        $setECReqDetails->Custom = $paypalCustomVariable;
        $setECReqDetails->PaymentDetails = $paymentDetail;

        //Type of Payment (https://developer.paypal.com/docs/classic/express-checkout/integration-guide/ECRelatedAPIOps/)
        $paymentDetail->PaymentAction = 'Sale';

        $setECReqDetails->CancelURL = \Config::get('paypal.cancel_url');
        $setECReqDetails->ReturnURL = \Config::get('paypal.return_url');

        //Shipping options if required.
        $setECReqDetails->NoShipping = 1;
        $setECReqDetails->ReqConfirmShipping = 0;

        //Set and configure express checkout
        $setECReqType = new SetExpressCheckoutRequestType();
        $setECReqType->SetExpressCheckoutRequestDetails = $setECReqDetails;
        $setECReq = new SetExpressCheckoutReq();
        $setECReq->SetExpressCheckoutRequest = $setECReqType;

        try {
            //Try Express Checkout.
            $setECResponse = $this->paypalService->SetExpressCheckout($setECReq);
        } catch (\Exception $ex) {
            throw new PaypalApiException();
        }

        //Check if we get a response from the server
        if (isset($setECResponse)) {
            //Check if we get a Successful acknowledgment from the server.
            if ($setECResponse->Ack == 'Success') {
                $paypalUrl = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=' . $setECResponse->Token;
                return \Redirect::away($paypalUrl);
            }
        } else {
            //Todo: return to the same page.
        }
    }

    public function getExpressCheckoutDetails()
    {
        $token = \Input::get('token');

        if (!$token) {
            \App::abort('Fatal Error.');
        }

        $getExpressCheckoutDetailsRequest = new GetExpressCheckoutDetailsRequestType($token);

        $getExpressCheckoutReq = new GetExpressCheckoutDetailsReq();
        $getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;

        try {
            $getECResponse = $this->paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);
        } catch (\Exception $ex) {
            throw new PaypalApiException();
        }

        if (isset($getECResponse)) {
            if ($getECResponse->Ack == 'Success') {
                $transactionId = $this->doExpressCheckout(
                    $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerID,
                    $getECResponse->GetExpressCheckoutDetailsResponseDetails->Token,
                    $getECResponse->GetExpressCheckoutDetailsResponseDetails->PaymentDetails[0]->OrderTotal
                );

                //Todo: add the details in database.

                if ($transactionId) {
                    echo $transactionId;
                } else {
                    throw new PaypalTransactionFailedException();
                }
            }
        }
    }

    protected function doExpressCheckout($payerId, $token, $orderTotal)
    {
        $paypalService = new PayPalAPIInterfaceServiceService(\Config::get('paypal'));

        $paymentDetail = new PaymentDetailsType();
        $paymentDetail->OrderTotal = $orderTotal;
        $paymentDetail->NotifyURL = \Config::get('paypal.notification_url');

        $DoECRequestDetails = new DoExpressCheckoutPaymentRequestDetailsType();
        $DoECRequestDetails->PayerID = $payerId;
        $DoECRequestDetails->Token = $token;
        $DoECRequestDetails->PaymentDetails[0] = $paymentDetail;

        $DoECRequest = new DoExpressCheckoutPaymentRequestType();
        $DoECRequest->DoExpressCheckoutPaymentRequestDetails = $DoECRequestDetails;


        $DoECReq = new DoExpressCheckoutPaymentReq();
        $DoECReq->DoExpressCheckoutPaymentRequest = $DoECRequest;

        try {
            $DoECResponse = $paypalService->DoExpressCheckoutPayment($DoECReq);
        } catch (\Exception $ex) {
            throw new PaypalApiException();
        }

        if (isset($DoECResponse)) {
            if ($DoECResponse->Ack == 'Success') {
                return $DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->TransactionID;
            } else {
                //Todo: Log errors
                return false;
            }
        } else {
            return false;
        }
    }
}
