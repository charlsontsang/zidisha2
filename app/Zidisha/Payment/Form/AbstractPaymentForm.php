<?php
namespace Zidisha\Payment\Form;


use Zidisha\Form\AbstractForm;
use Zidisha\Payment\Form\Validator\GreaterThanValidator;

abstract class AbstractPaymentForm extends AbstractForm
{

    protected $allowedServices = ['paypal', 'stripe'];

    public function getRules($data)
    {
        return [
            'amount'         => 'required|numeric',
            'donationAmount' => 'required|numeric',
            'transactionFee' => 'required|numeric',
            'totalAmount'    => 'required|numeric|GreaterThan:0',
            'paymentMethod'  => 'required|in:' . implode(',', $this->$allowedServices),
            'stripeToken'    => 'required_if:paymentMethod,stripe'
        ];
    }

    public function getDefaultData()
    {
        return [
            'amount'         => 0,
            'donationAmount' => 0,
            'totalAmount'    => 0,
            'paymentMethod'  => 'paypal',
            'transactionFee' => 3.5, //Todo: get this transaction fee from the config;
        ];
    }

    public function makePayment()
    {
        return $this->getService()->makePayment($this->getPayment());
    }

    public function getService()
    {
        if ($this->data['paymentService'] == 'paypal') {
            return new PaypalPaymentService();
        } else {
            if ($this->data['paymentService'] == 'stripe') {
                return new StripePaymentService();
            }
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
