<?php

namespace Zidisha\Borrower\Form;


use Illuminate\Support\Facades\Auth;
use Zidisha\Form\AbstractForm;
use Zidisha\Borrower\Base\ProfileQuery;
use Zidisha\Borrower\BorrowerQuery;

class EditProfile extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'username'      => 'required|alpha_num',
            'firstName'     => 'required|alpha_num',
            'lastName'      => 'required|alpha_num',
            'email'         => 'required|email',
            'password'      => 'confirmed',
            'aboutMe'       => '',
            'aboutBusiness' => '',
        ];
    }

    public function getDefaultData()
    {
        $borrower = BorrowerQuery::create()
            ->useUserQuery()
            ->filterById(\Auth::user()->getId())
            ->endUse()
            ->findOne();
        
        return [
            'username'      => $borrower->getUser()->getUsername(),
            'firstName'     => $borrower->getFirstName(),
            'lastName'      => $borrower->getLastName(),
            'email'         => $borrower->getUser()->getEmail(),
            'aboutMe'       => $borrower->getProfile()->getAboutMe(),
            'aboutBusiness' => $borrower->getProfile()->getAboutBusiness(),
        ];
    }
}
