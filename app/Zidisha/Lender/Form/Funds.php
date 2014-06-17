<?php
/**
 * Created by PhpStorm.
 * User: Singularity Guy
 * Date: 6/17/14
 * Time: 12:50 PM
 */

namespace Zidisha\Lender\Form;

use Zidisha\Form\AbstractForm;
use Zidisha\Lender\Form\Validator\FundsValidator;

class Funds extends AbstractForm{

    public function getRules($data)
    {
        return [
            'creditAmount'  => 'required|numeric',
            'feeAmount' => 'required|numeric',
            'donationAmount'  => 'required|numeric',
            'totalAmount' => 'required|numeric|Amounts'
        ];
    }

    public function getDefaultData()
    {
        return [
            'creditAmount'  => 100.00,
            'feeAmount' => 3.5,
            'donationAmount'  => 15,
            'totalAmount'     => 118.50,
            ];
    }

    protected function validate($data, $rules) {
        \Validator::resolver(function($translator, $data, $rules, $messages)
            {
                return new FundsValidator($translator, $data, $rules, $messages);
            });
        parent::validate($data, $rules);
    }
}