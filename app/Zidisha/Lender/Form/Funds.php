<?php

namespace Zidisha\Lender\Form;

use Illuminate\Http\Request;
use Zidisha\Form\AbstractForm;
use Zidisha\Lender\Form\Validator\FundsValidator;

class Funds extends AbstractForm
{

    public function getDataFromRequest(Request $request)
    {
        $data = parent::getDataFromRequest($request);
        $data['creditAmount'] = array_get($data, 'creditAmount', '') ?: 0;
        $data['donationAmount'] = array_get($data, 'donationAmount', '') ?: 0;
        
        return $data;
    }
    
    public function getRules($data)
    {
        return [
            'creditAmount' => 'required|numeric',
            'feeAmount' => 'required|numeric',
            'donationAmount' => 'required|numeric',
            'totalAmount' => 'required|numeric|Amounts',
            'paymentMethod' => 'required|in:stripe',
            'stripeToken' => 'required',
        ];
    }

    public function getDefaultData()
    {
        return [
            'creditAmount' => 100.00,
            'feeAmount' => 3.5,
            'donationAmount' => 15,
            'totalAmount' => 118.50,
        ];
    }

    protected function validate($data, $rules)
    {
        \Validator::resolver(function ($translator, $data, $rules, $messages) {
            return new FundsValidator($translator, $data, $rules, $messages);
        });
        parent::validate($data, $rules);
    }
}