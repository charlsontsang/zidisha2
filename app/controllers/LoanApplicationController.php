<?php

use Zidisha\Admin\Setting;
use Zidisha\Borrower\BorrowerService;
use Zidisha\Borrower\Form\Loan\ApplicationForm;
use Zidisha\Borrower\Form\Loan\ProfileForm;
use Zidisha\Borrower\Form\PersonalInformationForm;
use Zidisha\Currency\Money;
use Zidisha\Loan\Calculator\InstallmentCalculator;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\LoanService;
use Zidisha\Repayment\RepaymentSchedule;
use Zidisha\Upload\Upload;

class LoanApplicationController extends BaseBorrowerController
{
    use StepController;

    protected $steps = [
        'instructions',
        'profile',
        'application',
        'publish',
        'confirmation',
    ];

    private $loanService;
    /**
     * @var Zidisha\Borrower\BorrowerService
     */
    private $borrowerService;

    public function __construct(LoanService $loanService, BorrowerService $borrowerService)
    {
        $this->beforeFilter('@stepsBeforeFilter');
        $this->beforeFilter('@isNewLoanAllowedFilter');
        $this->beforeFilter('@validateOpenLoanFilter');
        
        $this->loanService = $loanService;
        $this->borrowerService = $borrowerService;
    }

    public function isNewLoanAllowedFilter()
    {
        $borrower = $this->getBorrower();
        
        if (!$borrower->isNewLoanAllowed()) {
            \Flash::error('You are not allowed to make new loan right now.');
            return Redirect::route('borrower:dashboard');
        }
    }

    public function validateOpenLoanFilter()
    {
        $borrower = $this->getBorrower();
        
        if (Session::has('borrower.openLoanId')) {
            $loan = $borrower->getActiveLoan();
            
            if (!$loan || !$loan->isOpen() || $loan->getId() != Session::get('borrower.openLoanId')) {
                //if validation fails, remove this from the session, flash an error and redirect to first step   
                Session::forget('borrower.openLoanId');
                
                \Flash::error('The loan is not valid.');
                return Redirect::action('LoanApplicationController@getInstructions');
            }
            
        }
    }

    protected function stepView($step, $params = array())
    {
        return View::make("borrower.loan.$step", ['steps' => $this->stepsData] + $params);
    }

    public function getInstructions()
    {
        $borrower = $this->getBorrower();

        $activeLoan = $borrower->getActiveLoan();
        
        if ($activeLoan) {
            Session::put('borrower.openLoanId', $activeLoan->getId());
        }
        
        return $this->stepView('instructions');
    }

    public function postInstructions()
    {
        $borrower = $this->getBorrower();
        
        $form = new PersonalInformationForm($borrower);
        $form->handleData($form->getDefaultData());
        
        $valid = true;
        
        if (!$form->isValid()) {
            \Flash::error('Your profile has some errors. Please fix them in order to continue the loan application.');
            $valid = false;
        }

        $isFacebookRequired = $this->borrowerService->isFacebookRequired($borrower);
        
        if ($isFacebookRequired) {
            \Flash::error('Facebook verification required.');
            $valid = false;
        }
        
        if (!$valid) {
            return Redirect::route('borrower:personal-information');
        }

        $this->setCurrentStep('profile');

        return Redirect::action('LoanApplicationController@getProfile');
    }

    public function getProfile()
    {
        $form = new ProfileForm();
        return $this->stepView('profile', ['form' => $form,]);
    }

    public function postProfile()
    {
        $form = new ProfileForm();
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();

            $user = $this->getUser();
            $borrower = $user->getBorrower();

            $borrower->getProfile()->setAboutMe($data['aboutMe']);
            $borrower->getProfile()->setAboutBusiness($data['aboutBusiness']);
            
            if (\Input::hasFile('picture')) {
                $upload = Upload::createFromFile(\Input::file('picture'));
                $upload->setUser($user);

                $user->setProfilePicture($upload);
                $user->save();
            }

            $borrower->save();

            $this->setCurrentStep('application');

            return Redirect::action('LoanApplicationController@getApplication');
        }

        return Redirect::action('LoanApplicationController@getProfile')->withForm($form);
    }

    public function getApplication()
    {
        $borrower = $this->getBorrower();

        if (Session::has('borrower.openLoanId')) {
            $form = new ApplicationForm($borrower, $borrower->getActiveLoan());
        } else {
            $form  = new ApplicationForm($borrower);
        }

        $isFirstLoan = LoanQuery::create()
            ->filterByBorrower($borrower)
            ->filterCompleted()
            ->count();
        $registrationFee = $isFirstLoan ? $borrower->getCountry()->getRegistrationFee() : Money::create(0, $borrower->getCountry()->getCurrencyCode());
        
        return $this->stepView('application', [
                'form' => $form,
                'installmentPeriod' => $form->isWeekly() ? 'weekly' : 'monthly',
                'registrationFee' => $registrationFee
            ]);
    }

    public function postApplication()
    {
        $form  = new ApplicationForm($this->getBorrower());
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();
            Session::set('loan_data', $data);

            $this->setCurrentStep('publish');

            return Redirect::action('LoanApplicationController@getPublish');
        }

        return Redirect::action('LoanApplicationController@getApplication')->withForm($form);
    }

    public function getPublish()
    {
        $data = Session::get('loan_data');
        $borrower = $this->getBorrower();

        $loan = $this->loanService->createLoan($borrower, $data);
        $loan
            ->setDisbursedAt(new \DateTime())
            ->setLenderInterestRate(Setting::get('loan.maximumLenderInterestRate'));

        $calculator = new InstallmentCalculator($loan);
        $repaymentSchedule = RepaymentSchedule::createFromInstallments($loan, $calculator->generateLoanInstallments());
        
        $data['installmentAmount'] = Money::create($data['installmentAmount'], $loan->getCurrencyCode());

        return $this->stepView('publish', compact('data', 'calculator', 'loan', 'repaymentSchedule'));
    }

    public function postPublish()
    {
        $data = Session::get('loan_data');

        $borrower = $this->getBorrower();

        if (Session::has('borrower.openLoanId')) {
            $loan = LoanQuery::create()
                ->findOneById(Session::get('borrower.openLoanId'));
            
            $this->loanService->updateLoanApplication($borrower, $loan, $data);            
        } else {
            $this->loanService->applyForLoan($borrower, $data);
        }

        Session::forget('loan_data');
        
        $this->setCurrentStep('confirmation');

        return Redirect::action('LoanApplicationController@getConfirmation');
    }

    public function getConfirmation()
    {
        $template = Session::has('borrower.openLoanId') ? 'confirmation-update' : 'confirmation';
        
        $borrower = $this->getBorrower();
        $loan = $borrower->getActiveLoan();

        $this->flushStepsSession();
        Session::forget('borrower.openLoanId');

        return $this->stepView($template, compact('borrower', 'loan'));
    }

    public function getInstallmentAmountRange()
    {
        $form  = new ApplicationForm($this->getBorrower());

        $amount = Input::get('amount');
        
        if (!$form->isValidAmount($amount)) {
            App::abort('404');
        }
        
        $amount = Money::create($amount, $form->getCurrency());
        $range = $form->getInstallmentAmountRange($amount);
        
        $options = [];
        foreach ($range as $k => $v) {
            $options[] = [$k, $v];
        }
        
        return Response::json($options);
    }
}
