<?php
namespace Zidisha\Payment\Stripe;

use Stripe_ApiConnectionError;
use Stripe_AuthenticationError;
use Stripe_CardError;
use Stripe_Error;
use Stripe_InvalidRequestError;
use Zidisha\Admin\Setting;
use Zidisha\Payment\Error\PaymentError;
use Zidisha\Payment\Payment;
use Zidisha\Payment\PaymentBus;
use Zidisha\Payment\PaymentService;
use Zidisha\Payment\Stripe\StripeLog;

class StripeService extends PaymentService
{
    private $paymentBus;

    public function __construct(PaymentBus $paymentBus)
    {
        $this->paymentBus = $paymentBus;
    }

    public function makePayment(Payment $payment, $data = [])
    {
        if (empty($data['stripeToken'])) {
            throw new \Exception('Missing Stripe Token.');
        }

        $stripeTransaction = new StripeTransaction();
        $stripeTransaction
            ->setAmount($payment->getCreditAmount())
            ->setDonationAmount($payment->getDonationAmount())
            ->setTransactionFee($payment->getTransactionFee())
            ->setTotalAmount($payment->getTotalAmount())
            ->setStatus('START')
            ->setPaymentId($payment->getId());
        $stripeTransaction->save();

        \Stripe::setApiKey(Setting::get('stripe.secretKey'));

        $paymentError = $charge = null;
        try {
            $charge = \Stripe_Charge::create(
                array(
                    "amount"   => $payment->getTotalAmount()->getAmountInCents(),
                    "currency" => "usd",
                    "card"     => $data['stripeToken'],
                )
            );
        } catch (Stripe_CardError $e) {
            $paymentError = new PaymentError('Your Card Information is not correct.');
        } catch (Stripe_InvalidRequestError $e) {
            $paymentError = new PaymentError('Invalid parameters give to stripe.');
        } catch (Stripe_AuthenticationError $e) {
            $paymentError = new PaymentError('Stripe Authentication failed.');
        } catch (Stripe_ApiConnectionError $e) {
            $paymentError = new PaymentError('We could not communicate with stripe please try again.');
        } catch (Stripe_Error $e) {
            //Todo: send mail to admin
            $paymentError = new PaymentError('Sorry we can not process your card at this moment.');
        } catch (\Exception $e) {
            //Todo: send mail to admin
            $paymentError = new PaymentError('Oops something is wrong.');
        }

        if ($paymentError) {
            return $this->paymentBus->getFailedHandler($payment, $paymentError)->setPayment($payment)->redirect();
        }

        $stripeTransaction
            ->setStatus('Completed')
            ->setStripeId($charge->id);
        $stripeTransaction->save();

        $serializedData = serialize($charge);

        //Stripe log
        $stripeLog = new StripeLog();
        $stripeLog->setLog($serializedData);
        $stripeLog->save();

        return $this->paymentBus->getCompletedHandler($payment)->process()->redirect();
    }
}
