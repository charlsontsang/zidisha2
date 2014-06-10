<?php

namespace Zidisha\Auth\Form;


use Zidisha\Form\AbstractForm;

class Join extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'email'    => 'required|email',
            'username' => 'required|max:20',
            'password' => 'required|confirmed'
        ];
    }

} 