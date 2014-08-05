<?php

namespace Zidisha\Lender\Form;


use Illuminate\Http\Request;
use Zidisha\Form\AbstractForm;
use Zidisha\Lender\Form\Validator\InvitesValidator;

class Invite extends AbstractForm {


    public function getRules($data)
    {
        return [
            'emails' => 'required|Emails',
            'subject' => '',
            'note' => 'required|min:1',
        ];
    }

    public function getDefaultData()
    {
        return [
            'subject' => 'Zidisha invitation + $25 lending credit',
        ];
    }

}