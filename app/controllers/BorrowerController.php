<?php


use Illuminate\Support\ViewErrorBag;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerActivationService;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\BorrowerService;
use Zidisha\Borrower\Form\EditProfile;
use Zidisha\Borrower\Form\PersonalInformationForm;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\Upload\UploadQuery;
use Zidisha\Vendor\Facebook\FacebookService;

class BorrowerController extends BaseController
{
    /**
     * @var Zidisha\Borrower\BorrowerService
     */
    private $borrowerService;

    /**
     * @var Zidisha\Mail\BorrowerMailer
     */
    private $borrowerMailer;
    /**
     * @var Zidisha\Vendor\Facebook\FacebookService
     */
    private $facebookService;
    /*
     * @var Zidisha\Borrower\BorrowerActivationService
     */
    private $borrowerActivationService;


    public function __construct(
        BorrowerService $borrowerService,
        BorrowerMailer $borrowerMailer,
        FacebookService $facebookService,
        BorrowerActivationService $borrowerActivationService
    ) {
        $this->borrowerService = $borrowerService;
        $this->borrowerMailer = $borrowerMailer;
        $this->facebookService = $facebookService;
        $this->borrowerActivationService = $borrowerActivationService;
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

        $form = new EditProfile($borrower);

        return View::make(
            'borrower.edit-profile',
            compact('form', 'borrower')
        );
    }

    public function postEditProfile()
    {
        $user = \Auth::user();
        $borrower = $user->getBorrower();
        $username = $user->getUsername();

        $form = new EditProfile($borrower);
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();

            $files = $this->getInputFiles();

            $this->borrowerService->editBorrower($borrower, $data, $files);

            return Redirect::route('borrower:public-profile', $username);
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
        /** @var Borrower $borrower */
        $borrower = \Auth::User()->getBorrower();

        $volunteerMentor = $borrower->getVolunteerMentor() ? $borrower->getVolunteerMentor()->getBorrowerVolunteer() : null;
        $feedbackMessages = [];

        $loan = $borrower->getActiveLoan();

        if ($loan){
            $feedbackMessages = $this->borrowerService->getFeedbackMessages($loan);
        }
        if ($borrower->isActivationPending()) {
            $feedbackMessages = $this->borrowerActivationService->getFeedbackMessages($borrower);
        }

        return View::make('borrower.dashboard', compact('borrower', 'volunteerMentor', 'feedbackMessages'));
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

    public function getPersonalInformation()
    {
        $borrower = \Auth::user()->getBorrower();

        $personalInformation = $borrower->getPersonalInformation();

        $form = new PersonalInformationForm($borrower);
        $form->handleData($form->getDefaultData());

        $errors = new ViewErrorBag();
        $errors->put('default', $form->getMessageBag());
        Session::flash('errors', $errors);

        $isFacebookRequired = $this->borrowerService->isFacebookRequired($borrower);

        $facebookJoinUrl = $this->facebookService->getLoginUrl(
            'borrower:facebook-verification',
            ['scope' => 'email,user_location,publish_stream,read_stream']
        );

        if ($isFacebookRequired) {
            \Flash::error('Facebook verification required.');
        }

        return \View::make(
            'borrower.personal-information',
            ['personalInformation' => $personalInformation, 'form' => $form, 'facebookJoinUrl' => $facebookJoinUrl, 'borrower' => $borrower, 'isFacebookRequired' => $isFacebookRequired]
        );
    }

    public function postPersonalInformation()
    {
        $borrower = \Auth::user()->getBorrower();

        $form = new PersonalInformationForm($borrower);

        $form->handleRequest(\Request::instance());

        if ($form->isValid()) {
            $data = $form->getNestedData();

            $this->borrowerService->updatePersonalInformation($borrower, $data);

            \Flash::success('Your profile has been updated.');
            return Redirect::route('borrower:personal-information');
        }

        return Redirect::route('borrower:personal-information')->withForm($form);
    }

    public function getFacebookRedirect()
    {
        $facebookUser = $this->facebookService->getUserProfile();

        if ($facebookUser) {
            $errors = $this->borrowerService->validateConnectingFacebookUser($facebookUser);

            if ($errors) {
                foreach ($errors as $error) {
                    Flash::error($error);
                }
                return Redirect::route('borrower:personal-information');
            }
        }

        $facebookId = $facebookUser['id'];

        $user = \Auth::user();

        $user->setFacebookId($facebookId);
        $user->save();

        \Flash::success('Your facebook account is linked successfully.');
        return Redirect::route('borrower:personal-information');
    }
}
