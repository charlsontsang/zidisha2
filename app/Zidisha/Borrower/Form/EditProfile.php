<?php

namespace Zidisha\Borrower\Form;


use Illuminate\Http\Request;
use Zidisha\Borrower\Borrower;
use Zidisha\Form\AbstractForm;
use Zidisha\Form\ZidishaValidator;

class EditProfile extends AbstractForm
{
    /**
     * @var Borrower
     */
    private $borrower;

    public function __construct(Borrower $borrower)
    {
        $this->borrower = $borrower;
    }

    public function getRules($data)
    {
        return [
            'email'         => 'required|email|uniqueUserEmail:' . $this->borrower->getId(),
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

    protected function validate($data, $rules)
    {
        \Validator::resolver(
            function ($translator, $data, $rules, $messages, $parameters) {
                return new ZidishaValidator($translator, $data, $rules, $messages, $parameters);
            }
        );

        parent::validate($data, $rules);
    }
}
