<?php
use Zidisha\Borrower\Form\Loan\Profile;

class LoanApplicationController extends BaseController
{
    use StepController;

    protected $steps = [
        'instructions',
        'profile',
        'application',
        'publish',
    ];

    private $editForm;

    public function __construct(Profile $form )
    {
        $this->beforeFilter('@stepsBeforeFilter');
        $this->editForm = $form;
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

    public function postProfile(){

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
        return $this->stepView('application');
    }
}
