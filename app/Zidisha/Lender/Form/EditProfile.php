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
            'username'  => 'required|alpha_num_space',
            'email'     => 'required|email|uniqueUserEmail:' . $this->lender->getId(),
            'password'  => 'confirmed',
            'city'      => 'required|alpha_num',
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
        /** @var $lender Lender */
        $lender = \Auth::user()->getLender();
        
        return [
            'username'  => $lender->getUser()->getUsername(),
            'email'     => $lender->getUser()->getEmail(),
            'city'      => $lender->getProfile()->getCity(),
            'aboutMe'   => $lender->getProfile()->getAboutMe(),
        ];
    }
}
