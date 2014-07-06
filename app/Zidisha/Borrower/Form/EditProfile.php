<?php

namespace Zidisha\Borrower\Form;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Zidisha\Form\AbstractForm;
use Zidisha\Borrower\Base\ProfileQuery;
use Zidisha\Borrower\BorrowerQuery;

class EditProfile extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'email'         => 'required|email',
            'password'      => 'confirmed',
            'aboutMe'       => '',
            'aboutBusiness' => '',
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
        $borrower = \Auth::user()->getBorrower();
        
        return [
            'email'         => $borrower->getUser()->getEmail(),
            'aboutMe'       => $borrower->getProfile()->getAboutMe(),
            'aboutBusiness' => $borrower->getProfile()->getAboutBusiness(),
        ];
    }
}
