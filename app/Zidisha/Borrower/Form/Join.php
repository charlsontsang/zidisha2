<?php
namespace Zidisha\Borrower\Form;


use Zidisha\Form\AbstractForm;

class Join extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'username' => 'required',
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required',
            'last_name' => 'required',
        ];
    }

    public function getDefaultData()
    {
        return [
            'email' => \Session::get('BorrowerJoin.email'),
        ];
    }
}