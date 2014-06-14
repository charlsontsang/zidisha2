<?php
namespace Zidisha\Borrower\Form\Join;


use Zidisha\Form\AbstractForm;

class Profile extends AbstractForm
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