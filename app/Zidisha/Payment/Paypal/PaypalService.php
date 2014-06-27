<?php
namespace Zidisha\Payment\Paypal;

use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\EBLBaseComponents\DoExpressCheckoutPaymentRequestDetailsType;
use PayPal\EBLBaseComponents\PaymentDetailsItemType;
use PayPal\EBLBaseComponents\PaymentDetailsType;
use PayPal\EBLBaseComponents\SetExpressCheckoutRequestDetailsType;
use PayPal\Exception\PPConnectionException;
use PayPal\IPN\PPIPNMessage;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentReq;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentRequestType;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsReq;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsRequestType;
use PayPal\PayPalAPI\SetExpressCheckoutReq;
use PayPal\PayPalAPI\SetExpressCheckoutRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;
use Zidisha\Currency\Money;
use Zidisha\Payment\Error\PaymentError;
use Zidisha\Payment\Payment;
use Zidisha\Payment\PaymentBus;
use Zidisha\Payment\PaymentService;

class PayPalService extends PaymentService
{
    /**
     * @var \PayPal\Service\PayPalAPIInterfaceServiceService
     */
    private $payPalApi;

    /**
     * @var \Zidisha\Payment\PaymentBus
     */
    private $paymentBus;

    public function __construct(PaymentBus $paymentBus)
    {
        $this->payPalApi = new PayPalAPIInterfaceServiceService(\Config::get('paypal'));
        $this->paymentBus = $paymentBus;
    }

    public function makePayment(Payment $payment, array $data = null )
    {
        $payPalCustom = md5($payment->getId() . '-' . time());

        $payPalTransaction = new PaypalTransaction();
        $payPalTransaction
            ->setAmount($payment->getAmount())
            ->setDonationAmount($payment->getDonationAmount())
            ->setPaypalTransactionFee($payment->getTransactionFee())
            ->setTotalAmount($payment->getTotalAmount())
            ->setStatus('START')
            ->setCustom($payPalCustom)
            ->setPaymentId($payment->getId());
        $payPalTransaction->save();

        $paymentDetail = $this->setPaypalCheckoutCart($payment);

        $expressCheckoutRequestDetails = new SetExpressCheckoutRequestDetailsType();
        $expressCheckoutRequestDetails->Custom = $payPalCustom;
        $expressCheckoutRequestDetails->PaymentDetails = $paymentDetail;

        $expressCheckoutRequestDetails->CancelURL = action('PayPalController@getCancel');
        $expressCheckoutRequestDetails->ReturnURL = action('PayPalController@getReturn');

        // Display options
        $expressCheckoutRequestDetails->cpplogoimage = 'https://www.zidisha.org/static/images/logo/zidisha-logo-small.png';
        $expressCheckoutRequestDetails->BrandName = 'Zidisha';

        //Shipping options if required.
        $expressCheckoutRequestDetails->NoShipping = 1;
        $expressCheckoutRequestDetails->ReqConfirmShipping = 0;

        //Set and configure express checkout
        $setExpressCheckoutRequestType = new SetExpressCheckoutRequestType();
        $setExpressCheckoutRequestType->SetExpressCheckoutRequestDetails = $expressCheckoutRequestDetails;
        $setExpressCheckoutRequest = new SetExpressCheckoutReq();
        $setExpressCheckoutRequest->SetExpressCheckoutRequest = $setExpressCheckoutRequestType;

        try {
            //Try Express Checkout.
            $setExpressCheckoutResponse = $this->payPalApi->SetExpressCheckout($setExpressCheckoutRequest);
        } catch (PPConnectionException $e) {
            $paymentError = new PaymentError('Error Connecting to PayPal.', $e);
            return $this->paymentBus->getFailedHandler($payment)->redirect($paymentError);
        }

        //Check if we get a Successful acknowledgment from the server.
        if ($setExpressCheckoutResponse->Ack != 'Success') {
            $this->logPayPalErrors($payment, $setExpressCheckoutResponse);

            $paymentError = new PaymentError('Error Connecting to PayPal.');
            return $this->paymentBus->getFailedHandler($payment)->redirect($paymentError);
        }

        $payPalTransaction
            ->setToken($setExpressCheckoutResponse->Token);
        $payPalTransaction->save();

        $paypalUrl = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=' . $setExpressCheckoutResponse->Token;

        return \Redirect::away($paypalUrl);
    }

    public function getExpressCheckoutDetails($token)
    {
        $paypalTransaction = PaypalTransactionQuery::create()
            ->findOneByToken($token);

        if (!$paypalTransaction) {
            \App::abort('Fatal Error');
        }

        $payment = $paypalTransaction->getPayment();

        $getExpressCheckoutDetailsRequest = new GetExpressCheckoutDetailsRequestType($token);

        $getExpressCheckoutRequest = new GetExpressCheckoutDetailsReq();
        $getExpressCheckoutRequest->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;

        try {
            $getExpressCheckoutResponse = $this->payPalApi->GetExpressCheckoutDetails($getExpressCheckoutRequest);
        } catch (PPConnectionException $e) {
            $paymentError = new PaymentError('Error Connecting to PayPal.', $e);
            return $this->paymentBus->getFailedHandler($payment)->redirect($paymentError);
        }

        if ($getExpressCheckoutResponse->Ack != 'Success') {
            $paymentError = new PaymentError('Error Connecting to PayPal.');
            return $this->paymentBus->getFailedHandler($payment)->redirect($paymentError);
        }

        try {
            $payPalFinalResponse = $this->doExpressCheckout(
                $getExpressCheckoutResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerID,
                $getExpressCheckoutResponse->GetExpressCheckoutDetailsResponseDetails->Token,
                $getExpressCheckoutResponse->GetExpressCheckoutDetailsResponseDetails->PaymentDetails[0]->OrderTotal
            );
        } catch (PPConnectionException $e) {
            $paymentError = new PaymentError('Error Connecting to PayPal.', $e);
            return $this->paymentBus->getFailedHandler($payment)->redirect($paymentError);
        }

        if ($payPalFinalResponse->Ack != "Success") {
            $this->logPayPalErrors($payment, $payPalFinalResponse);

            $paymentError = new PaymentError('PayPal transaction failed.');
            return $this->paymentBus->getFailedHandler($payment)->redirect($paymentError);
        }

        $paymentStatus = $payPalFinalResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->PaymentStatus;
        if ($paymentStatus != "Completed") {
            $paymentError = new PaymentError('PayPal transaction pending');
            return $this->paymentBus->getPendingHandler($payment)->redirect($paymentError);
        }

        $paypalTransaction->setStatus($paymentStatus);
        $paypalTransaction->save();

        return $this->paymentBus->getCompletedHandler($payment)->redirect();
    }

    protected function doExpressCheckout($payerId, $token, $orderTotal)
    {
        $paymentDetail = new PaymentDetailsType();
        $paymentDetail->OrderTotal = $orderTotal;
        $paymentDetail->NotifyURL = $this->getIpnUrl();

        $DoExpressCheckoutRequestDetails = new DoExpressCheckoutPaymentRequestDetailsType();
        $DoExpressCheckoutRequestDetails->PayerID = $payerId;
        $DoExpressCheckoutRequestDetails->Token = $token;
        $DoExpressCheckoutRequestDetails->PaymentDetails[0] = $paymentDetail;

        $DoExpressCheckoutPaymentRequestType = new DoExpressCheckoutPaymentRequestType();
        $DoExpressCheckoutPaymentRequestType->DoExpressCheckoutPaymentRequestDetails = $DoExpressCheckoutRequestDetails;

        $DoExpressCheckoutPaymentReq = new DoExpressCheckoutPaymentReq();
        $DoExpressCheckoutPaymentReq->DoExpressCheckoutPaymentRequest = $DoExpressCheckoutPaymentRequestType;

        return $this->payPalApi->DoExpressCheckoutPayment($DoExpressCheckoutPaymentReq);
    }

    /**
     * @param Payment $payment
     * @return PaymentDetailsType
     */
    protected function setPaypalCheckoutCart(Payment $payment)
    {
        $paymentDetail = new PaymentDetailsType();

        if ($payment->getAmount()->greaterThan(Money::create(0))) {
            $itemDetail = new PaymentDetailsItemType();
            $itemDetail->Name = 'Lend To Zidisha';
            $itemDetail->Amount = new BasicAmountType('USD', $payment->getAmount()->round(2)->getAmount());
            $itemDetail->Quantity = '1';
            $itemDetail->ItemCategory = 'Digital';
            $paymentDetail->PaymentDetailsItem[] = $itemDetail;
        }

        if ($payment->getDonationAmount()->greaterThan(Money::create(0))) {
            $itemDetail = new PaymentDetailsItemType();
            $itemDetail->Name = 'Donation To Zidisha';
            $itemDetail->Amount = new BasicAmountType('USD', $payment->getDonationAmount()->round(2)->getAmount());
            $itemDetail->Quantity = '1';
            $itemDetail->ItemCategory = 'Digital';
            $paymentDetail->PaymentDetailsItem[] = $itemDetail;
        }

        if ($payment->getTransactionFee()->greaterThan(Money::create(0))) {
            $itemDetail = new PaymentDetailsItemType();
            $itemDetail->Name = ' Zidisha Transaction Fee';
            $itemDetail->Amount = new BasicAmountType('USD', $payment->getTransactionFee()->round(2)->getAmount());
            $itemDetail->Quantity = '1';
            $itemDetail->ItemCategory = 'Digital';
            $paymentDetail->PaymentDetailsItem[] = $itemDetail;
        }

        //Add this item to payment and set the order amount
        $paymentDetail->OrderTotal = new BasicAmountType('USD', $payment->getTotalAmount()->round(2)->getAmount());
        $paymentDetail->NotifyURL = $this->getIpnUrl();

        //Type of Payment (https://developer.paypal.com/docs/classic/express-checkout/integration-guide/ECRelatedAPIOps/)
        $paymentDetail->PaymentAction = 'Sale';

        return $paymentDetail;
    }

    public function processIpn(PPIPNMessage $ipnMessage)
    {
        $data = $ipnMessage->getRawData();

        if ($ipnMessage->validate()) {
            //Log PayPal Notifications
            $payPalLog = new PaypalNotification();
            $payPalLog->setNotification(serialize($data));
            $payPalLog->save();

            $custom = $data['custom'];
            $transactionId = $data['txn_id'];
            $transactionType = $data['txn_type'];
            $paymentStatus = $data['payment_status'];

            $paypalTransaction = PaypalTransactionQuery::create()
                ->findOneByCustom($custom);

            $paypalTransaction
                ->setTransactionId($transactionId)
                ->setTransactionType($transactionType)
                ->setStatus($paymentStatus);
            $paypalTransaction->save();

            $payment = $paypalTransaction->getPayment();

            if ($paymentStatus == 'Completed') {
                $this->paymentBus->getCompletedHandler($payment)->process();
            } elseif ($paymentStatus == 'Failed') {
                $this->paymentBus->getFailedHandler($payment)->process();
            }
        } else {
            \Log::error("Error: Got invalid IPN data");
            \Log::error($data);
        }

        return \Response::make('', 200);
    }

    public function getCancel($token)
    {
        $paypalTransaction = PaypalTransactionQuery::create()
            ->findOneByToken($token);

        $paypalTransaction->setStatus('Canceled');
        $paypalTransaction->save();

        $payment = $paypalTransaction->getPayment();

        $paymentError = new PaymentError('Canceled PayPal transaction.');
        return $this->paymentBus->getFailedHandler($payment)->redirect($paymentError);
    }

    protected function logPayPalErrors($response)
    {
        //Todo: mail to admin.
        \Log::error('PaypalError');
        \Log::error('Time: ' . $response->Timestamp);

        foreach ($response->Errors as $error) {
            \Log::error('Error Code: ' . $error->ErrorCode);
            \Log::error('Error Short Message: ' . $error->ShortMessage);
            \Log::error('Error Long Message: ' . $error->LongMessage);
        }
    }

    /**
     * @return mixed
     */
    protected function getIpnUrl()
    {
        return \Config::get('paypal.ipn_url', action('PayPalController@postIpn'));
    }
}
