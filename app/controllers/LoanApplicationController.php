<?php
use Zidisha\Borrower\Form\Loan\ApplicationForm;
use Zidisha\Borrower\Form\Loan\ProfileForm;
use Zidisha\Loan\Calculator\InstallmentCalculator;
use Zidisha\Loan\Loan;
use Zidisha\Loan\CategoryQuery;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\LoanService;

class LoanApplicationController extends BaseController
{
    use StepController;

    protected $steps = [
        'instructions',
        'profile',
        'application',
        'publish',
        'confirmation',
    ];

    private $editForm, $applicationForm;
    /**
     * @var Zidisha\Loan\LoanService
     */
    private $loanService;

    public function __construct(ProfileForm $form, LoanService $loanService)
    {
        $this->beforeFilter('@stepsBeforeFilter');
        $this->beforeFilter('@isNewLoanAllowedFilter');
        $this->editForm = $form;
        $this->applicationForm = new ApplicationForm(\Auth::user()->getBorrower());
        $this->loanService = $loanService;
    }

    protected function stepView($step, $params = array())
    {
        return View::make("borrower.loan.$step", ['steps' => $this->stepsData] + $params);
    }

    public function getInstructions()
    {
        return $this->stepView('instructions');
    }

    public function postInstructions()
    {
        $this->setCurrentStep('profile');

        return Redirect::action('LoanApplicationController@getProfile');
    }

    public function getProfile()
    {
        return $this->stepView('profile', ['form' => $this->editForm,]);
    }

    public function postProfile()
    {
        $form = $this->editForm;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();

            $borrower = Auth::user()->getBorrower();

            $borrower->getProfile()->setAboutMe($data['aboutMe']);
            $borrower->getProfile()->setAboutBusiness($data['aboutBusiness']);

            $borrower->save();

            $this->setCurrentStep('application');

            return Redirect::action('LoanApplicationController@getApplication');
        }

        return Redirect::action('LoanApplicationController@getProfile')->withForm($form);

    }

    public function getApplication()
    {
        return $this->stepView('application', ['form' => $this->applicationForm,]);
    }

    public function postApplication()
    {
        $form = $this->applicationForm;
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
        $borrower = Auth::user()->getBorrower();

        //TODO: fetch maximum interest rate from settings
        $maxInterestRate = 20;

        $loan = $this->loanService->createLoan($borrower, $data);
        $loan->setDisbursedAt(new \DateTime());

        $calculator = new InstallmentCalculator($loan);
        $installments = $this->loanService->generateLoanInstallments($loan);

        return $this->stepView('publish', compact('data', 'calculator', 'installments', 'loan', 'maxInterestRate'));
    }

    public function postPublish()
    {
        $data = Session::get('loan_data');

        $borrower = Auth::user()->getBorrower();

        $this->loanService->applyForLoan($borrower, $data);

        $this->setCurrentStep('confirmation');

        return Redirect::action('LoanApplicationController@getConfirmation');
    }

    public function getConfirmation()
    {
        return $this->stepView('confirmation');
    }
}
