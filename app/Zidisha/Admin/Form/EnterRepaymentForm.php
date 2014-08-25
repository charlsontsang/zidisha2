<?php

namespace Zidisha\Admin\Form;


use Zidisha\Form\AbstractForm;

class EnterRepaymentForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'date'   => 'required|date_format:m/d/Y',
            'amount' => 'required|numeric|greaterThan:0',
        ];
    }
}
