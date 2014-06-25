<?php
namespace Zidisha\Payment\Form\Validator;

use Illuminate\Validation\Validator;

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
}
