<?php

namespace Zidisha\Lender\Form;


use Zidisha\Form\AbstractForm;

class GiftCard extends AbstractForm
{


    public function getRules($data)
    {
        return [
            'amount' => 'required|in:' . implode(',', array_keys($this->getAmounts())),
            'orderType' => 'required|in:Email,Self-Print',
            'template' => 'required|in:' . implode(',', array_keys($this->getTemplates())),
            'recipientEmail' => 'email|required_if:orderType,Email',
            'recipientName' => '',
            'fromName' => '',
            'message' => '',
            'confirmationEmail' => 'email',
        ];
    }

    public function getDefaultData()
    {
        return [
            'orderType' => 'Email',
            'recipientName' => null,
            'recipientEmail' => null,
            'fromName' => null,
            'message' => null,
            'confirmationEmail' => null,
        ];
    }


    public function getAmounts()
    {
        $array = [
            1 => '$1',
            5 => '$5',
            10 => '$10',
            25 => '$25',
            30 => '$30',
            50 => '$50',
            75 => '$75',
            100 => '$100',
            150 => '$150',
            200 => '$200',
            250 => '$250',
            300 => '$300',
            400 => '$400',
            500 => '$500',
            1000 => '$1,000',
            5000 => '$5,000',
            10000 => '$10,000',
        ];

        return $array;
    }

    public function getOrderTypes()
    {
        $array = [
            'Self-Print',
            'Email',
        ];

        return array_combine($array, $array);
    }

    public function getTemplates()
    {
        $array = range(2,9);

        return array_combine($array, $array);
    }

}
