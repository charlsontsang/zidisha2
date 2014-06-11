<?php

namespace Zidisha\Lender\Form;


use Illuminate\Support\Facades\Auth;
use Zidisha\Form\AbstractForm;
use Zidisha\Lender\Base\ProfileQuery;
use Zidisha\Lender\LenderQuery;

class EditProfile extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'username'  => 'required|alpha_num',
            'firstName' => 'required|alpha_num',
            'lastName'  => 'required|alpha_num',
            'email'     => 'required|email',
            'password'  => 'confirmed',
            'aboutMe'   => '',
        ];
    }

    public function getDefaultData()
    {
        $lender = \Auth::user()->getLender();
        
        return [
            'username'  => $lender->getUser()->getUsername(),
            'firstName' => $lender->getFirstName(),
            'lastName'  => $lender->getLastName(),
            'email'     => $lender->getUser()->getEmail(),
            'aboutMe'   => $lender->getProfile()->getAboutMe(),
        ];
    }
}
