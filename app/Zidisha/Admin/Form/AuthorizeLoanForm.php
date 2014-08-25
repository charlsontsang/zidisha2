<?php

namespace Zidisha\Admin\Form;


use Zidisha\Form\AbstractForm;

class AuthorizeLoanForm extends AbstractForm
{
    public function getRules($data)
    {
        return [
            'authorizedAt'     => 'required|date_format:m/d/Y',
            'authorizedAmount' => 'required|numeric|greaterThan:0',
        ];
    }
}
