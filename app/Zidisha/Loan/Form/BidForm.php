<?php

namespace Zidisha\Loan\Form;

use Zidisha\Form\AbstractForm;
use Zidisha\Loan\Form\Validator\BidValidator;
use Zidisha\Loan\Loan;

class BidForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'amount' => 'required|numeric|Amount',
            'interestRate' => 'required|numeric|max:15',
            'loanId' => 'exists:loans,id,status,' . Loan::OPEN,
        ];
    }

    public function getDefaultData()
    {
        return [
            'amount' => 20.00,
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