<?php
/**
 * Created by PhpStorm.
 * User: Singularity Guy
 * Date: 6/18/14
 * Time: 1:31 PM
 */

namespace Zidisha\Loan\Form\Validator;

use Illuminate\Validation\Validator;

class BidValidator extends Validator
{

    public function validateAmount($attribute, $value, $parameters)
    {
        return $value > 0;
    }

    protected function replaceAmount($message, $attribute, $rule, $parameters)
    {
        return 'Amount should be greater than zero!';
    }

} 