<?php
namespace Zidisha\Payment\Form\Validator;

use Zidisha\Currency\Money;
use Zidisha\Form\ZidishaValidator;

class PaymentValidator extends ZidishaValidator
{
    public function validateAssertTotal($attribute, $value, $parameters)
    {
        return Money::create($this->data['creditAmount'])
            ->add(Money::create($this->data['donationAmount']))
            ->add(Money::create($this->data['transactionFee']))
            ->equals(Money::create($this->data['totalAmount']));
    }

    protected function replaceAssertTotal($message, $attribute, $rule, $parameters)
    {
        return $attribute . ' should be equal to sum of Amount, Transaction Fee and Donation Amount.';
    }

    public function validateTotalFee($attribute, $value, $parameters)
    {
        //Todo: get transaction fee rate from the Configuration.
        return Money::create($this->data['creditAmount'])
            ->multiply($this->data['transactionFeeRate'])
            ->equals(Money::create($this->data['transactionFee']));
    }

    protected function replaceTotalFee($message, $attribute, $rule, $parameters)
    {
        return $attribute . ' total fee should be amount times the rate.';
    }
    
    public function validateCreditAmount($attribute, $value, $parameters)
    {
        $amount = Money::create($this->data['amount']);
        $creditAmount = Money::create($this->data['creditAmount']);
        $currentBalance = Money::create($this->data['currentBalance']);

        $amountDifference = $amount->greaterThan($currentBalance) ? $amount->subtract($currentBalance) : Money::create(0);

        return $creditAmount->equals($amountDifference);
    }

    protected function replaceCreditAmount($message, $attribute, $rule, $parameters)
    {
        return 'Credit amount does not match.';
    }
}
