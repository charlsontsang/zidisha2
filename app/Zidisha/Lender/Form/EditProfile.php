<?php

namespace Zidisha\Lender\Form;


use Zidisha\Form\AbstractForm;
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
        $lender = LenderQuery::create()
            ->useUserQuery()
            ->filterById(\Auth::user()->getId())
            ->endUse()
            ->findOne();
        
        return [
            'username'  => $lender->getUser()->getUsername(),
            'firstName' => $lender->getFirstName(),
            'lastName'  => $lender->getLastName(),
            'email'     => $lender->getUser()->getEmail(),
            'aboutMe'   => $lender->getAboutMe(),
        ];
    }
}
