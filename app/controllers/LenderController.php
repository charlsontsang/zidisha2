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
    
    public function getPublicProfile($username)
    {


        $lender = LenderQuery::create()
            ->useUserQuery()
                ->filterByUsername($username)
            ->endUse()
            ->findOne();

        if(!$lender){
            \Illuminate\Support\Facades\App::abort(404);
        }
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

            $lender = Auth::user()->getLender();

            $lender->setFirstName($data['firstName']);
            $lender->setLastName($data['lastName']);
            $lender->getUser()->setEmail($data['email']);
            $lender->getUser()->setUsername($data['username']);
            $lender->getProfile()->setAboutMe($data['aboutMe']);

            if (!empty($data['password'])) {
                $lender->getUser()->setPassword($data['password']);
            }

            $lender->save();

            if(Input::hasFile('picture'))
            {
                $image = Input::file('picture');
                $image->move(public_path() . '/images/profile/', $data['username'].'.jpg' );
            }

            return Redirect::route('lender:public-profile', $data['username']);
        }

        return Redirect::route('lender:edit-profile')->withForm($form);
    }

    public function getDashboard(){
        return View::make('lender.dashboard');
    }
}
