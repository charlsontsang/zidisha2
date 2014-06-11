<?php

use Zidisha\Lender\Form\EditProfile;
use Zidisha\Lender\LenderQuery;
use Zidisha\Lender\ProfileQuery;

class LenderController extends BaseController
{

    /**
     * @var Zidisha\Lender\Form\EditProfile
     */
    private $editProfileForm;

    public function __construct(EditProfile $editProfileForm)
    {
        $this->editProfileForm = $editProfileForm;
    }
    
    public function getPublicProfile()
    {
        $lender = LenderQuery::create()
            ->useUserQuery()
                ->filterById(Auth::user()->getId())
            ->endUse()
            ->findOne();

        return View::make(
            'lender.public-profile',
            compact('lender')
        );
    }

    public function getEditProfile()
    {
        return View::make(
            'lender.edit-profile',
            ['form' => $this->editProfileForm,]
        );
    }

    public function postEditProfile()
    {
        $form = $this->editProfileForm;
        $form->handleRequest(Request::instance());
        
        if ($form->isValid()) {
            $data = $form->getData();
            
            $lender = LenderQuery::create()
                ->useUserQuery()
                    ->filterById(Auth::user()->getId())
                ->endUse()
                ->findOne();

            $lender->setFirstName($data['firstName']);
            $lender->setLastName($data['lastName']);
            $lender->getUser()->setEmail($data['email']);
            $lender->getUser()->setUsername($data['username']);
            $lender->getProfile()->setAboutMe($data['aboutMe']);

            if (!empty($data['password'])) {
                $lender->getUser()->setPassword($data['password']);
            }

            $lender->save();

            return Redirect::route('lender:public-profile');
        }

        return Redirect::route('lender:edit-profile')->withForm($form);
    }

    public function getDashboard(){
        return View::make('lender.dashboard');
    }
}
