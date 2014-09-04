<?php

namespace Zidisha\Borrower\Form\Loan;


use Illuminate\Support\Facades\Auth;
use Zidisha\Form\AbstractForm;

class ProfileForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'aboutMe'       => 'required|min:300',
            'aboutBusiness' => 'required|min:300',
        ];
    }

    public function getDefaultData()
    {
        $borrower = Auth::user()->getBorrower();

        return [
            'aboutMe'       => $borrower->getProfile()->getAboutMe(),
            'aboutBusiness' => $borrower->getProfile()->getAboutBusiness(),
        ];
    }
}
