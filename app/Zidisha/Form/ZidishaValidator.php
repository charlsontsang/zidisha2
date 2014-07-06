<?php

namespace Zidisha\Form;


use Illuminate\Validation\Validator;

class ZidishaValidator extends Validator {

    public function validateEmails($attribute, $value, $parameters)
    {
        $emails = explode(",", $value);
        
        foreach($emails as $email)
        {
            $email = trim($email);
            
            if (!$this->validateEmail(null, $email)) {
                return false;
            }
        }

        return true;
    }
    
}
