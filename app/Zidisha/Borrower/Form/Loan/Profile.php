<?php

namespace Zidisha\Borrower\Form\Loan;


use Illuminate\Support\Facades\Auth;
use Zidisha\Form\AbstractForm;
use Zidisha\Borrower\Base\ProfileQuery;
use Zidisha\Borrower\BorrowerQuery;

class Profile extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'aboutMe'       => 'min:300',
            'aboutBusiness' => 'min:300',
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
            'aboutMe'       => $borrower->getProfile()->getAboutMe(),
            'aboutBusiness' => $borrower->getProfile()->getAboutBusiness(),
        ];
    }
}
