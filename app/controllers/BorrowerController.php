<?php

use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\BorrowerService;
use Zidisha\Borrower\Form\EditProfile;
use Zidisha\Borrower\JoinLogQuery;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\Upload\UploadQuery;

class BorrowerController extends BaseController
{
    /**
     * @var Zidisha\Borrower\Form\EditProfile
     */
    private $editProfileForm;
    /**
     * @var Zidisha\Borrower\BorrowerService
     */
    private $borrowerService;

    /**
     * @var Zidisha\Mail\BorrowerMailer
     */
    private $borrowerMailer;

    public function __construct(
        EditProfile $editProfileForm,
        BorrowerService $borrowerService,
        BorrowerMailer $borrowerMailer
    ) {
        $this->editProfileForm = $editProfileForm;
        $this->borrowerService = $borrowerService;
        $this->borrowerMailer = $borrowerMailer;
    }

    public function getPublicProfile($username)
    {
        $borrower = BorrowerQuery::create()
            ->useUserQuery()
            ->filterByUsername($username)
            ->endUse()
            ->findOne();

        if (!$borrower) {
            App::abort(404);
        }

        return View::make(
            'borrower.public-profile',
            compact('borrower')
        );
    }

    public function getEditProfile()
    {
        $borrower = \Auth::user()->getBorrower();

        return View::make(
            'borrower.edit-profile',
            ['form' => $this->editProfileForm, 'borrower' => $borrower]
        );
    }

    public function postEditProfile()
    {
        $form = $this->editProfileForm;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();

            $borrower = \Auth::user()->getBorrower();

            $files = $this->getInputFiles();

            $this->borrowerService->editBorrower($borrower, $data, $files);

            return Redirect::route('borrower:public-profile', $data['username']);
        }

        return Redirect::route('borrower:edit-profile')->withForm($form);
    }

    protected function getInputFiles()
    {
        $files = [];
        if (\Input::hasFile('images')) {
            foreach (\Input::file('images') as $file) {
                if (!empty($file)) {
                    if ($file->isValid() && $file->getSize() < Config::get('image.allowed-file-size')) {
                        $files[] = $file;
                    } else {
                        Flash::error(\Lang::get('borrower.flash.file-not-valid'));
                    }
                }
            }
            return $files;
        }
        return $files;
    }

    public function getDashboard()
    {
        $borrower = \Auth::User()->getBorrower();
        $verified = $borrower->getVerified();

        $volunteerMentor = $borrower->getVolunteerMentor() ? $borrower->getVolunteerMentor()->getBorrowerVolunteer() : null;
        $feedbackMessages = null;

        $loan = $borrower->getActiveLoan();

        if($loan){
            $feedbackMessages = $this->borrowerService->getFeedbackMessages($loan);
        }

        return View::make('borrower.dashboard', compact('verified', 'volunteerMentor', 'feedbackMessages'));
    }

    public function getTransactionHistory()
    {
        return View::make('borrower.history');
    }

    public function postDeleteUpload()
    {
        $borrower = BorrowerQuery::create()->filterById(\Input::get('borrower_id'))->findOne();
        $upload = UploadQuery::create()->filterById(\Input::get('upload_id'))->findOne();

        $user = \Auth::user();

        if (!$borrower || !$upload) {
            App::abort(404, 'Bad Request');
        }

        $this->borrowerService->deleteUpload($borrower, $upload);

        Flash::success(\Lang::get('borrower.flash.file-deleted'));
        return Redirect::back();
    }

    public function resendVerificationMail()
    {
        $borrower = \Auth::user()->getBorrower();

        $this->borrowerService->sendVerificationCode($borrower);

        \Flash::info('A verification code has been sent to your email. Please check your email.');
        return \Redirect::action('BorrowerController@getDashboard');
    }
}
