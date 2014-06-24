<?php

namespace Zidisha\Lender\Form\Validator;


use Illuminate\Validation\Validator;

class InvitesValidator extends Validator {

    public function validateEmails($attribute, $value, $parameters)
    {
        $emails = explode(",", $value);
        foreach($emails as $email)
        {
            $email = trim($email);
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                return false;
            }
        }

        return true;
    }
}
