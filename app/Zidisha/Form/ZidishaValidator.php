<?php

namespace Zidisha\Form;


use Illuminate\Validation\Validator;
use Propel\Runtime\ActiveQuery\Criteria;
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

    public function validateCheckCommentRating($attribute, $value, $parameters)
    {
        switch ($value) {
            case LoanFeedbackComment::NEUTRAL :
                return true;
            case LoanFeedbackComment::NEGATIVE :
                return true;
            case LoanFeedbackComment::POSITIVE :
                return true;
        }

        return false;
    }

    protected function replaceCheckCommentRating($message, $attribute, $rule, $parameters)
    {
        return $attribute . ' type is not correct';
    }
}
