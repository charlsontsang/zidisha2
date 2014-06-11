<?php

use Zidisha\Borrower\Form\EditProfile;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\ProfileQuery;

class BorrowerController extends BaseController
{

    private $editProfileForm;

    public function __construct(EditProfile $editProfileForm)
    {
        $this->editProfileForm = $editProfileForm;
    }

    public function getPublicProfile()
    {
        $borrower = BorrowerQuery::create()
            ->useUserQuery()
            ->filterById(Auth::user()->getId())
            ->endUse()
            ->findOne();

        return View::make(
            'borrower.public-profile',
            compact('borrower')
        );
    }

    public function getEditProfile()
    {
        return View::make(
            'borrower.edit-profile',
            ['form' => $this->editProfileForm,]
        );
    }

    public function postEditProfile()
    {
        $form = $this->editProfileForm;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();

            $borrower = \Auth::user()->getBorrower();

            $borrower->setFirstName($data['firstName']);
            $borrower->setLastName($data['lastName']);
            $borrower->getUser()->setEmail($data['email']);
            $borrower->getUser()->setUsername($data['username']);
            $borrower->getProfile()->setAboutMe($data['aboutMe']);
            $borrower->getProfile()->setAboutBusiness($data['aboutBusiness']);

            if (!empty($data['password'])) {
                $borrower->getUser()->setPassword($data['password']);
            }

            $borrower->save();

            return Redirect::route('borrower:public-profile');
        }

        return Redirect::route('borrower:edit-profile')->withForm($form);
    }

    public function getDashboard(){
        return View::make('borrower.dashboard');
    }
}
