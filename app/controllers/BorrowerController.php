<?php

use Zidisha\Borrower\BorrowerService;
use Zidisha\Borrower\Form\EditProfile;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\ProfileQuery;

class BorrowerController extends BaseController
{

    private $editProfileForm;
    /**
     * @var Zidisha\Borrower\BorrowerService
     */
    private $borrowerService;

    public function __construct(EditProfile $editProfileForm, BorrowerService $borrowerService)
    {
        $this->editProfileForm = $editProfileForm;
        $this->borrowerService = $borrowerService;
    }

    public function getPublicProfile($username)
    {
        $borrower = BorrowerQuery::create()
            ->useUserQuery()
            ->filterByUsername($username)
            ->endUse()
            ->findOne();

        if(!$borrower){
            App::abort(404);
        }

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

            $this->borrowerService->editBorrower($borrower, $data);

            if(Input::hasFile('picture'))
            {
                $image = Input::file('picture');
                $this->borrowerService->uploadPicture($borrower, $image);
            }

            return Redirect::route('borrower:public-profile' , $data['username']);
        }

        return Redirect::route('borrower:edit-profile')->withForm($form);
    }

    public function getDashboard(){
        return View::make('borrower.dashboard');
    }

    public function getTransactionHistory(){
        return View::make('borrower.history');
    }
}
