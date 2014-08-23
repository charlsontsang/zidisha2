<?php

namespace Zidisha\Admin\Form;

use Zidisha\Form\AbstractForm;

class ForgiveLoanForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'countryCode' => 'required|exists:countries,country_code',
            'loanId'      => 'required|exists:loans,id',
            'comment'     => ''
        ];
    }
}

