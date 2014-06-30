<?php
namespace Zidisha\Borrower\Form\Join;


use Zidisha\Form\AbstractForm;

class ProfileForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'username'             => 'required|unique:users,username',
            'password'             => 'required',
            'email'                => 'required|email|unique:users,email',
            'firstName'            => 'required',
            'lastName'             => 'required',
            'address'              => 'required|min:20',
            'addressInstruction'   => 'required|min:50',
            'city'                 => 'required',
            'nationalIdNumber'     => 'required',
            'phoneNumber'          => 'required|numeric',
            'alternatePhoneNumber' => 'required|numeric',
        ];
    }

    public function getDefaultData()
    {
        return [
            'email' => \Session::get('BorrowerJoin.email'),
        ];
    }
}