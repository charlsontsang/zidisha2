<?php

namespace Zidisha\Loan\Form;

use Zidisha\Form\AbstractForm;
use Zidisha\Loan\Form\Validator\BidValidator;

class BidForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'Amount' => 'required|numeric|Amount',
            'interestRate' => 'required|numeric|max:15',
            'loanId' => '',
        ];
    }

    public function getDefaultData()
    {
        return [
            'Amount' => 20.00,
            'interestRate' => 3,
        ];
    }

    public function getRates()
    {
        $keys = range(0, 15);
        $values = array_map(
            function ($a) {
                return "$a%";
            },
            $keys
        );

        return array_combine($keys, $values);
    }


    protected function validate($data, $rules)
    {
        \Validator::resolver(
            function ($translator, $data, $rules, $messages, $parameters) {
                return new BidValidator($translator, $data, $rules, $messages, $parameters);
            }
        );
        parent::validate($data, $rules);
    }

} 