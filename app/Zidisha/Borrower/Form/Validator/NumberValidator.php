<?php
namespace Zidisha\Borrower\Form\Validator;

use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Borrower\ProfileQuery;
use Zidisha\Form\ZidishaValidator;
use Zidisha\User\UserQuery;

class NumberValidator extends ZidishaValidator
{
    public function validateUniqueNumber($attribute, $value, $parameters)
    {
        $id = $parameters[0];

        $checkPhoneNumber = ProfileQuery::create()
            ->filterByBorrowerId( $id, Criteria::NOT_EQUAL)
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

    public function validateUniqueEmail($attribute, $value, $parameters)
    {
        $id = $parameters[0];

        $userEmail = UserQuery::create()
            ->filterById( $id, Criteria::NOT_EQUAL)
            ->filterByEmail($value)
            ->count();

        if ($userEmail) {
            return false;
        }
        return true;
    }

    protected function ReplaceUniqueEmail($message, $attribute, $rule, $parameters)
    {
        return $attribute . ' already exits in the database.';
    }

    public function validateMutualUniqueNumber($attribute, $value, $parameters)
    {
        $phoneNumber = array_get($this->data, 'phoneNumber');
        $alternatePhoneNumber = array_get($this->data, 'alternatePhoneNumber');

        return $phoneNumber != $alternatePhoneNumber;
    }

    public function validateContactUniqueNumber($attribute, $value, $parameters)
    {
        $numbers = [
             'communityLeader_phoneNumber' => array_get($this->data, 'communityLeader_phoneNumber'),
             'familyMember_1_phoneNumber'  => array_get($this->data, 'familyMember_1_phoneNumber'),
             'familyMember_2_phoneNumber'  => array_get($this->data, 'familyMember_2_phoneNumber'),
             'familyMember_3_phoneNumber'  => array_get($this->data, 'familyMember_3_phoneNumber'),
             'neighbor_1_phoneNumber'      => array_get($this->data, 'neighbor_1_phoneNumber'),
             'neighbor_2_phoneNumber'      => array_get($this->data, 'neighbor_2_phoneNumber'),
             'neighbor_3_phoneNumber'      => array_get($this->data, 'neighbor_3_phoneNumber'),
        ];

        foreach ($numbers as $field => $number ){
            if($number === $value && $attribute != $field){
                return false;
            }
        }

        return true;
    }

    protected function replaceMutualUniqueNumber($message, $attribute, $rule, $parameters)
    {
        return 'Phone number and alternate phone number should be unique.';
    }

    protected function replaceContactUniqueNumber($message, $attribute, $rule, $parameters)
    {
        return 'Phone numbers should be unique.';
    }
}
