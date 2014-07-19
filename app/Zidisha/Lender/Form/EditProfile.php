<?php

namespace Zidisha\Lender\Form;


use Illuminate\Http\Request;
use Zidisha\Form\AbstractForm;
use Zidisha\Form\ZidishaValidator;
use Zidisha\Lender\Lender;

class EditProfile extends AbstractForm
{

    /**
     * @var Lender
     */
    private $lender;

    public function __construct(Lender $lender)
    {
        $this->lender = $lender;
    }

    public function getRules($data)
    {
        return [
            'username'  => 'required|alpha_num',
            'firstName' => 'required|alpha_num',
            'lastName'  => 'required|alpha_num',
            'email'     => 'required|email|uniqueUserEmail:' . $this->lender->getId(),
            'password'  => 'confirmed',
            'aboutMe'   => '',
            'picture'   => 'image|max:2048',
        ];
    }

    public function getDataFromRequest(Request $request) {
        $data = parent::getDataFromRequest($request);
        $data['picture'] = $request->file('picture');

        return $data;
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
