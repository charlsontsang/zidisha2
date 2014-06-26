<?php

namespace Zidisha\Lender\Form;


use Zidisha\Form\AbstractForm;

class GiftCard extends AbstractForm
{


    public function getRules($data)
    {
        return [
            'amount' => 'required',
            'deliveryMethod' => 'required',
            'recipientEmail' => 'email',
            'toName' => '',
            'fromName' => '',
            'message' => '',
            'yourEmail' => 'email',
        ];
    }

    public function getDefaultData()
    {
        return [

        ];
    }


    public function getAmounts()
    {
        $array = [
            '$1',
            '$5',
            '$10',
            '$25',
            '$30',
            '$50',
            '$75',
            '$100',
            '$150',
            '$200',
            '$250',
            '$300',
            '$400',
            '$500',
            '$1,000',
            '$5,000',
            '$10,000',
        ];

        return $array;
    }
}
