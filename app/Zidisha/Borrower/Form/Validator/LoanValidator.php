<?php
namespace Zidisha\Borrower\Form\Validator;

use Illuminate\Validation\Validator;

class LoanValidator extends Validator
{
    public function validateGreaterThan($attribute, $value, $parameters)
    {
        return $value > $parameters[0];
    }

    protected function replaceGreaterThan($message, $attribute, $rule, $parameters)
    {
        return $attribute . ' should be greater than zero.';
    }
    
    public function validateMinimumInstallmentAmount($attribute, $value, $parameters)
    {
        // TODO use Loan->calculateInstallmentCount
        return array_get($this->data, 'amount') / $value <= $parameters[0];
    }

    protected function replaceMinimumInstallmentAmount($message, $attribute, $rule, $parameters)
    {
        $maximumPeriod = $parameters[0];
        $period = $parameters[1];
        
        return "Please increase the installment amount until the repayment period is at maximum $maximumPeriod $period";
    }
}
