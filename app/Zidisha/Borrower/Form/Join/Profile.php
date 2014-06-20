<?php
namespace Zidisha\Borrower\Form\Join;


use Zidisha\Form\AbstractForm;

class Profile extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'username' => 'required|unique:users,username',
            'password' => 'required',
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required|min:20',
            'address_instruction' => 'required|min:50',
            'village' => 'required',
            'national_id_number' => 'required',
            'phone_number' => 'required|numeric',
            'alternate_phone_number' => 'required|numeric',
        ];
    }

    public function getDefaultData()
    {
        return [
            'email' => \Session::get('BorrowerJoin.email'),
        ];
    }
}