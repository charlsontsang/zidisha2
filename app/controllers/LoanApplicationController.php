<?php
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\Form\Loan\ApplicationForm;
use Zidisha\Borrower\Form\Loan\ProfileForm;
use Zidisha\Currency\Money;
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

    private $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->beforeFilter('@stepsBeforeFilter');
        $this->loanService = $loanService;
        $this->isNewLoanAllowedFilter();
        $this->validateOpenLoanFilter();
    }

    public function isNewLoanAllowedFilter()
    {
        $borrower = \Auth::user()->getBorrower();

        if (!$borrower->isNewLoanAllowed()) {
            \Flash::error('You are not allowed to make new loan right now.');
            return Redirect::route('borrower:dashboard');
        }
    }

    public function validateOpenLoanFilter()
    {
        /** @var Borrower $borrower */
        $borrower = \Auth::user()->getBorrower();
        
        if (Session::has('borrower.openLoanId')) {
            $loan = $borrower->getActiveLoan();
            
            if (! $loan->getStatus() == Loan::OPEN) {
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
        $this->isNewLoanAllowedFilter();
        
        /** @var Borrower $borrower */
        $borrower = \Auth::user()->getBorrower();

        $activeLoan = $borrower->getActiveLoan();
        
        if ($activeLoan && $activeLoan->getStatus() == Loan::OPEN) {
            Session::put('borrower.openLoanId', $activeLoan->getId());
        }
        
        return $this->stepView('instructions');
    }

    public function postInstructions()
    {
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

        /** @var Borrower $borrower */
        $borrower = Auth::user()->getBorrower();

        if (Session::has('borrower.openLoanId')) {
            $loan = LoanQuery::create()
                ->findOneById(Session::get('borrower.openLoanId'));

            $form = new ApplicationForm($borrower, $loan);
        } else {
            $form  = new ApplicationForm(\Auth::user()->getBorrower());
        }
        
        return $this->stepView('application', ['form' => $form,]);
    }

    public function postApplication()
    {
        $form  = new ApplicationForm(\Auth::user()->getBorrower());
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

        $loan = $this->loanService->createLoan($borrower, $data);
        $loan
            ->setDisbursedAt(new \DateTime())
            ->setLenderInterestRate(Setting::get('loan.maximumLenderInterestRate'));

        $calculator = new InstallmentCalculator($loan);
        $installments = $calculator->generateLoanInstallments($loan);

        return $this->stepView('publish', compact('data', 'calculator', 'installments', 'loan'));
    }

    public function postPublish()
    {
        $data = Session::get('loan_data');

        $borrower = Auth::user()->getBorrower();

        if (Session::has('borrower.openLoanId')) {
            $loan = LoanQuery::create()
                ->findOneById(Session::get('borrower.openLoanId'));
            
            $this->loanService->updateLoanApplication($borrower, $loan, $data);            
        } else {
            $this->loanService->applyForLoan($borrower, $data);
        }
        
        $this->setCurrentStep('confirmation');

        return Redirect::action('LoanApplicationController@getConfirmation');
    }

    public function getConfirmation()
    {
        return $this->stepView('confirmation');
    }

    public function getInstallmentAmountRange()
    {
        $form  = new ApplicationForm(\Auth::user()->getBorrower());

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
