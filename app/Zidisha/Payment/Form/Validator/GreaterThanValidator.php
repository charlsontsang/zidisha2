<?php
namespace Zidisha\Payment\Form\Validator;

use Illuminate\Validation\Validator;
use Zidisha\Currency\Money;

class GreaterThanValidator extends Validator
{
    public function validateGreaterThan($attribute, $value, $parameters)
    {
        return $value > $parameters[0];
    }

    protected function replaceGreaterThan($message, $attribute, $rule, $parameters)
    {
        return $attribute.' should be greater than zero.';
    }

    public function validateAssertTotal($attribute, $value, $parameters)
    {
        return Money::create($this->data['amount'])
            ->add(Money::create($this->data['donationAmount']))
            ->add(Money::create($this->data['transactionFee']))
            ->equals(Money::create($this->data['totalAmount']));
    }

    protected function replaceAssertTotal($message, $attribute, $rule, $parameters)
    {
        return $attribute.' should be equal to sum of Amount, Transaction Fee and Donation Amount.';
    }

    public function validateTotalFee($attribute, $value, $parameters)
    {
        //Todo: get transaction fee rate from the Configuration.
        return Money::create($this->data['amount'])
            ->multiply($this->data['transactionFeeRate'])
            ->equals(Money::create($this->data['transactionFee']));
    }

    protected function replaceTotalFee($message, $attribute, $rule, $parameters)
    {
        return $attribute.' Total fee should be amount times the rate.';
    }
}
