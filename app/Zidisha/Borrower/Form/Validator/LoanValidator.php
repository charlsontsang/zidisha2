<?php
namespace Zidisha\Borrower\Form\Validator;

use Zidisha\Form\ZidishaValidator;

class LoanValidator extends ZidishaValidator
{

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
