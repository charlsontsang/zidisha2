<?php
namespace Zidisha\Payment\Form;


use Zidisha\Balance\TransactionQuery;
use Zidisha\Currency\Money;
use Zidisha\Form\AbstractForm;
use Zidisha\Payment\Form\Validator\GreaterThanValidator;
use Zidisha\Payment\PaymentBus;
use Zidisha\Payment\Paypal\PayPalService;
use Zidisha\Payment\Stripe\StripeService;

abstract class AbstractPaymentForm extends AbstractForm
{
    protected $currentBalance;
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
            'creditAmount' => 'required|numeric|creditAmount',
            'donationAmount' => 'required|numeric',
            'transactionFee' => 'required|numeric|totalFee',
            'totalAmount' => 'required|numeric|assertTotal|greaterThan:0',
            'paymentMethod' => 'required|in:'. implode(',', $this->allowedServices),
            'stripeToken' => 'required_if:paymentMethod,stripe',
            'transactionFeeRate' => '',
            'amount' => '',
        ];
    }

    public function getDefaultData()
    {
        return [
            'creditAmount' => 0,
            'donationAmount' => 0,
            'totalAmount' => 0,
            'paymentMethod' => 'paypal',
            'transactionFeeRate' => 0.035, //Todo: get this transaction fee from the config;
            'amount' => 30,
            'currentBalance' => $this->getCurrentBalance()->getAmount()
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

    /**
     * @return \Zidisha\payment\payment $payment
     */
    abstract public function getPayment();

    protected function validate($data, $rules)
    {
        $data['currentBalance'] = $this->getCurrentBalance();

        \Validator::resolver(
            function ($translator, $data, $rules, $messages, $parameters) {
                return new GreaterThanValidator($translator, $data, $rules, $messages, $parameters);
            }
        );

        parent::validate($data, $this->getRules($data));
    }

    public function getCurrentBalance()
    {
        if ($this->currentBalance === null) {
            if (!\Auth::check()) {
                $this->currentBalance = Money::create(0);
            } else {
                $this->currentBalance = TransactionQuery::create()
                    ->filterByUserId(\Auth::user()->getId())
                    ->getTotalAmount();
            }
        }

        return $this->currentBalance;
    }
}
