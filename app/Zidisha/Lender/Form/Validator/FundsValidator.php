<?php

namespace Zidisha\Lender\Form\Validator;


use Illuminate\Validation\Validator;

class FundsValidator extends Validator
{

    public function validateAmounts($attribute, $value, $parameters)
    {
        $subtotalAmount = $this->getValue('donationAmount') + $this->getValue('creditAmount');
        $feeAmount = $subtotalAmount * 0.025;
        $totalAmount = $subtotalAmount + $feeAmount;

        if ($this->getValue('feeAmount') != round($feeAmount, 2)) {
            return false;
        }

        if ($this->getValue('totalAmount') != round($totalAmount, 2)) {
            return false;
        }
        
        return true;
    }

}
