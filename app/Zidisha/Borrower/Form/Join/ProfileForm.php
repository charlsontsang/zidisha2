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
            'address'              => 'required',
            'addressInstruction'   => 'required',
            'city'                 => 'required',
            'nationalIdNumber'     => 'required|unique:borrower_profiles,national_id_number',
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