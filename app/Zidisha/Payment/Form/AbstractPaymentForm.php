<?php
namespace Zidisha\Payment\Form;


use Zidisha\Form\AbstractForm;
use Zidisha\Payment\Form\Validator\AssertTotalValidator;
use Zidisha\Payment\Form\Validator\GreaterThanValidator;
use Zidisha\Payment\PaymentBus;
use Zidisha\Payment\Paypal\PayPalService;
use Zidisha\Payment\Stripe\StripeService;

abstract class AbstractPaymentForm extends AbstractForm
{
    /**
     * @var \Zidisha\Payment\Paypal\PayPalService
     */
    private $payPalService;
    /**
     * @var \Zidisha\Payment\Stripe\StripeService
     */
    private $stripeService;

    protected $allowedServices = ['paypal', 'stripe'];

    public function getRules($data)
    {
        return [
            'amount' => 'required|numeric',
            'donationAmount' => 'required|numeric',
            'transactionFee' => 'required|numeric|totalFee',
            'totalAmount' => 'required|numeric|assertTotal|greaterThan:0',
            'paymentMethod' => 'required|in:'. implode(',', $this->allowedServices),
            'stripeToken' => 'required_if:paymentMethod,stripe',
            'transactionFeeRate' => '',
        ];
    }

    public function getDefaultData()
    {
        return [
            'amount' => 0,
            'donationAmount' => 0,
            'totalAmount' => 0,
            'paymentMethod' => 'paypal',
            'transactionFeeRate' => 0.035, //Todo: get this transaction fee from the config;
        ];
    }

    public function makePayment()
    {
        $payment = $this->getPayment();
        $payment->setPaymentMethod($this->data['paymentMethod']);
        $payment->save();

        return $this->getService()->makePayment($payment, ['stripeToken' => array_get($this->data, 'stripeToken')]);
    }

    public function getService()
    {
        $paymentBus = new PaymentBus();

        if ($this->data['paymentMethod'] == 'paypal') {
            return new PayPalService($paymentBus);
        } elseif ($this->data['paymentMethod'] == 'stripe') {
            return new StripeService($paymentBus);
        }

        throw new \Exception();
    }

    abstract public function getPayment();

    protected function validate($data, $rules)
    {
        \Validator::resolver(
            function ($translator, $data, $rules, $messages, $parameters) {
                return new GreaterThanValidator($translator, $data, $rules, $messages, $parameters);
            }
        );

        parent::validate($data, $this->getRules($data));
    }
}
