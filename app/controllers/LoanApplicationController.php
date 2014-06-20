<?php
use Zidisha\Borrower\Form\Loan\Application;
use Zidisha\Borrower\Form\Loan\Profile;
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

    public function __construct(Profile $form, Application $applicationForm, LoanService $loanService)
    {
        $this->beforeFilter('@stepsBeforeFilter');
        $this->editForm = $form;
        $this->applicationForm = $applicationForm;
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

        return $this->stepView('publish', compact('data'));
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
