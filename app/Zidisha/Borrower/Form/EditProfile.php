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
            'username'      => 'required|alpha_num',
            'firstName'     => 'required|alpha_num',
            'lastName'      => 'required|alpha_num',
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
            'username'      => $borrower->getUser()->getUsername(),
            'firstName'     => $borrower->getFirstName(),
            'lastName'      => $borrower->getLastName(),
            'email'         => $borrower->getUser()->getEmail(),
            'aboutMe'       => $borrower->getProfile()->getAboutMe(),
            'aboutBusiness' => $borrower->getProfile()->getAboutBusiness(),
        ];
    }
}
