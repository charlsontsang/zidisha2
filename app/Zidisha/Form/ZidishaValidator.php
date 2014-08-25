<?php

namespace Zidisha\Form;


use Illuminate\Validation\Validator;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Borrower\ProfileQuery;
use Zidisha\Comment\LoanFeedbackComment;
use Zidisha\User\UserQuery;

class ZidishaValidator extends Validator
{

    /**
     * @var AbstractForm
     */
    protected $form;

    /**
     * @return AbstractForm
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param mixed AbstractForm
     */
    public function setForm(AbstractForm $form)
    {
        $this->form = $form;

        return $this;
    }

    public function validateEmails($attribute, $value, $parameters)
    {
        $emails = explode(",", $value);

        foreach ($emails as $email) {
            $email = trim($email);

            if (!$this->validateEmail(null, $email)) {
                return false;
            }
        }

        return true;
    }

    public function validateGreaterThan($attribute, $value, $parameters)
    {
        return $value > $parameters[0];
    }

    protected function replaceGreaterThan($message, $attribute, $rule, $parameters)
    {
        return $attribute . ' should be greater than ' . $parameters[0] . '.';
    }

    public function validateUniqueUserEmail($attribute, $value, $parameters)
    {
        $id = $parameters[0];

        $userEmailCount = UserQuery::create()
            ->filterById($id, Criteria::NOT_EQUAL)
            ->filterByEmail($value)
            ->count();

        return $userEmailCount == 0 ? true : false;
    }

    protected function replaceUniqueUserEmail($message, $attribute, $rule, $parameters)
    {
        return $attribute . ' already exits in the database.';
    }

    public function validateUniqueEmail($attribute, $value, $parameters)
    {
        $userEmailCount = UserQuery::create()
            ->filterByEmail($value)
            ->count();

        return $userEmailCount == 0 ? true : false;
    }

    protected function replaceUniqueEmail($message, $attribute, $rule, $parameters)
    {
        return $attribute . ' already exits in the database.';
    }

    public function validateCheckInterestRate($attribute, $value, $parameters)
    {
        $interestRate = $this->getValue($attribute);

        if ($interestRate == 'other') {
            return true;
        } else {
            if (in_array($interestRate, [0,3,5,10]) && $interestRate <= 100) {
                return true;
            }
        }
    }
    
    protected function replaceCheckInterestRate($message, $attribute, $rule, $parameters)
    {
        return $attribute . ' is not correct';
    }

    public function validateUniqueNumber($attribute, $value, $parameters)
    {
        $id = $parameters[0];

        $phoneNumberCount = ProfileQuery::create()
            ->filterByBorrowerId($id, Criteria::NOT_EQUAL)
            ->filterByPhoneNumber($value)
            ->_or()
            ->filterByAlternatePhoneNumber($value)
            ->count();

        return $phoneNumberCount == 0 ? true : false;
    }

    protected function replaceUniqueNumber($message, $attribute, $rule, $parameters)
    {
        return $attribute . ' already exits in the database.';
    }
}
