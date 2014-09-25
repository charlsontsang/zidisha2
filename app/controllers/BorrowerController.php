<?php


use Illuminate\Support\ViewErrorBag;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerActivationService;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\BorrowerService;
use Zidisha\Borrower\Calculator\CreditLimitCalculator;
use Zidisha\Borrower\Form\EditProfile;
use Zidisha\Borrower\Form\PersonalInformationForm;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\LoanService;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\Repayment\RepaymentService;
use Zidisha\Upload\UploadQuery;
use Zidisha\User\User;
use Zidisha\Vendor\Facebook\FacebookService;

class BorrowerController extends BaseController
{
    private $borrowerService;
    private $borrowerMailer;
    private $facebookService;
    private $borrowerActivationService;
    private $repaymentService;
    private $loanService;


    public function __construct(
        BorrowerService $borrowerService,
        BorrowerMailer $borrowerMailer,
        FacebookService $facebookService,
        BorrowerActivationService $borrowerActivationService,
        RepaymentService $repaymentService,
        LoanService $loanService
    ) {
        $this->borrowerService = $borrowerService;
        $this->borrowerMailer = $borrowerMailer;
        $this->facebookService = $facebookService;
        $this->borrowerActivationService = $borrowerActivationService;
        $this->repaymentService = $repaymentService;
        $this->loanService = $loanService;
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
        /** @var User $user */
        $user = \Auth::user();
        $borrower = $user->getBorrower();
        $username = $user->getUsername();

        $form = new EditProfile($borrower);
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();

            $files = [];
//            $files = $this->getInputFiles();

            $this->borrowerService->editBorrower($borrower, $data, \Input::file('picture'), $files);

            if ($borrower->getLastLoanId()) {
                return Redirect::route('loan:index', $borrower->getLastLoanId());
            }
            return Redirect::route('borrower:dashboard');
        }

        return Redirect::route('borrower:edit-profile')->withForm($form);
    }

//    protected function getInputFiles()
//    {
//        $files = [];
//        if (\Input::hasFile('images')) {
//            foreach (\Input::file('images') as $file) {
//                if (!empty($file)) {
//                    if ($file->isValid() && $file->getSize() < Config::get('image.allowed-file-size')) {
//                        $files[] = $file;
//                    } else {
//                        Flash::error(\Lang::get('borrower.flash.file-not-valid'));
//                    }
//                }
//            }
//            return $files;
//        }
//        return $files;
//    }

    public function getDashboard()
    {
        /** @var Borrower $borrower */
        $borrower = \Auth::User()->getBorrower();
        if (!$borrower) {
            App::abort(404);
        }

        $volunteerMentor = $borrower->getVolunteerMentor() ? $borrower->getVolunteerMentor()->getBorrowerVolunteer() : null;
        $feedbackMessages = [];

            $loan = LoanQuery::create()
                ->findLastLoan($borrower);

        $partial = 'loan-no-loan';
        
        $partials = [
            Loan::OPEN      => 'loan-open',
            Loan::FUNDED    => 'loan-funded',
            Loan::ACTIVE    => 'loan-active',
            Loan::REPAID    => 'loan-repaid',
            Loan::NO_LOAN   => 'loan-no-loan',
            Loan::DEFAULTED => 'loan-defaulted',
            Loan::CANCELED  => 'loan-canceled',
            Loan::EXPIRED   => 'loan-expired',
        ];

        if ($loan) {
            $feedbackMessages = $this->borrowerService->getFeedbackMessages($loan);
            $partial = array_get($partials, $loan->getStatus());
        }
        if ($borrower->isActivationPending()) {
            $feedbackMessages = $this->borrowerActivationService->getFeedbackMessages($borrower);
        }
        
        $data = compact(
            'borrower',
            'volunteerMentor',
            'feedbackMessages',
            'partial',
            'loan'
        );
        
        if ($loan && ($loan->isActive() || $loan->isDefaulted() || $loan->isRepaid())) {
            $repaymentSchedule = $this->repaymentService->getRepaymentSchedule($loan);

            $data['repaymentSchedule'] = $repaymentSchedule;
        }

        return View::make('borrower.dashboard.dashboard', $data);
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

        \Flash::info(\Lang::get('common.comments.flash.borrower-join-email-sent'));
        return \Redirect::action('BorrowerController@getDashboard');
    }

    public function getPersonalInformation($userId = null)
    {
        $isVisitor = false;
        $isAdmin = false;
        if ($userId) {
            $borrower = BorrowerQuery::create()
                ->findOneById($userId);
            /** @var User $user */
            $user = \Auth::user();
            if ($user->isVolunteerMentor()) {
                $isVisitor = true;
            } else {
                $isAdmin = true;
            }
        } else {
            /** @var Borrower $borrower */
            $borrower = \Auth::user()->getBorrower();
        }
        if (!$borrower) {
            App::abort(404);
        }
        $personalInformation = $borrower->getPersonalInformation();

        $form = new PersonalInformationForm($borrower);
        if ($isVisitor) {
            $form->setVisitor(true);
        } elseif ($isAdmin) {
            $form->setAdmin(true);
        }
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
            \Flash::error('join.form.facebook-intro');
        }

        return \View::make(
            'borrower.personal-information',
            compact('personalInformation', 'form', 'facebookJoinUrl', 'borrower', 'isFacebookRequired', 'isVisitor')
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

            \Flash::success('borrower.loan-application.info-saved');
            return Redirect::route('borrower:personal-information');
        }

        \Flash::error('common.validation.incomplete-profile');
        return Redirect::route('borrower:personal-information')->withForm($form);
    }

    public function getPersonalInformationAdmin($username)
    {
        $borrower = BorrowerQuery::create()
            ->useUserQuery()
            ->filterByUsername($username)
            ->endUse()
            ->findOne();

        $form = new PersonalInformationForm($borrower);

        $form->handleRequest(\Request::instance());

        if ($form->isValid()) {
            $data = $form->getNestedData();

            $this->borrowerService->updatePersonalInformation($borrower, $data);

            \Flash::success('Profile has been updated.');
            return Redirect::route('admin:borrower:personal-information', $borrower->getId());
        }
        \Flash::error('Profile has some errors.');
        return Redirect::route('admin:borrower:personal-information', $borrower->getId())->withForm($form);
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

        \Flash::success('common.validation.link-account.facebook-account-linked');
        return Redirect::route('borrower:personal-information');
    }

    public function getCurrentCredit()
    {
        /** @var $borrower Borrower */
        $borrower = \Auth::user()->getBorrower();
        
        $exchangeRate = ExchangeRateQuery::create()
            ->findCurrent($borrower->getCountry()->getCurrency());
        
        $calculator = new CreditLimitCalculator($borrower, $exchangeRate);
        
        $replacements = [
            'repaymentRate'    => $calculator->getSufficientRepaymentRate(),
            'minRepaymentRate' => $calculator->getMinimumRepaymentRate(),
            'minLoanLength'    => $calculator->getMinLoanLength(),
        ];

        return View::make('borrower.current-credit', compact(
            'calculator',
            'replacements'
        ));
    }
}
