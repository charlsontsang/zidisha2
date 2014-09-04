<?php

namespace Zidisha\Borrower\Form\Loan;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Zidisha\Form\AbstractForm;

class ProfileForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'aboutMe'       => 'required|min:300',
            'aboutBusiness' => 'required|min:300',
            'picture'       => 'image|max:2048',
        ];
    }

    public function getDataFromRequest(Request $request) {
        $data = parent::getDataFromRequest($request);
        $data['picture'] = $request->file('picture');

        return $data;
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
