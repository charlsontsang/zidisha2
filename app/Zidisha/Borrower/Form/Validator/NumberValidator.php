<?php
namespace Zidisha\Borrower\Form\Validator;

use Illuminate\Validation\Validator;
use Zidisha\Borrower\ProfileQuery;

class NumberValidator extends Validator
{
    public function validateUniqueNumber($attribute, $value, $parameters)
    {
        $checkPhoneNumber = ProfileQuery::create()
            ->filterByPhoneNumber($value)
            ->_or()
            ->filterByAlternatePhoneNumber($value)
            ->count();

        if ($checkPhoneNumber) {
            return false;
        }
        return true;
    }

    protected function replaceUniqueNumber($message, $attribute, $rule, $parameters)
    {
        return $attribute . ' already exits in the database.';
    }

    public function validateMutualUniqueNumber($attribute, $value, $parameters)
    {
        $phoneNumber = array_get($this->data, 'phoneNumber');
        $alternatePhoneNumber = array_get($this->data, 'alternatePhoneNumber');

        return $phoneNumber != $alternatePhoneNumber;
    }

    protected function replaceMutualUniqueNumber($message, $attribute, $rule, $parameters)
    {
        return 'Phone number and alternate phone number should be unique.';
    }
}
