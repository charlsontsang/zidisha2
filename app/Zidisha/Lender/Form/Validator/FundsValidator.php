<?php
/**
 * Created by PhpStorm.
 * User: Singularity Guy
 * Date: 6/17/14
 * Time: 3:12 PM
 */

namespace Zidisha\Lender\Form\Validator;


use Illuminate\Validation\Validator;

class FundsValidator extends Validator{

    public function validateAmounts($attribute, $value, $parameters){
        if($this->getValue('feeAmount') != ((($this->getValue('donationAmount') + $this->getValue('creditAmount'))*2.5)/100)){
            return false;
        }

        if($value != ($this->getValue('creditAmount') + $this->getValue('feeAmount') + $this->getValue('donationAmount'))){
            return false;
        }
        return true;
    }

} 