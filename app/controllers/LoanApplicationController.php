<?php

class LoanApplicationController extends BaseController
{

    protected $steps = array();

    public function __construct()
    {
        $this->beforeFilter('@validateSteps');
    }
    
    protected function getStepFromRoute($route)
    {
        $controllerMethod = $route->getAction()['controller'];
        list($controller, $method) = explode('@', $controllerMethod);
        
        return preg_replace('/^(get|post)_/', '', snake_case($method));
    }
    
    public function validateSteps($route)
    {
        $steps = [
            'instructions',
            'profile',
            'application',
            'publish',
        ];
        
        $routeStep = $this->getStepFromRoute($route);
        
        $currentStep = Session::get('loanapplication.currentStep', $steps[0]);

        $state = 'complete';
        $this->steps = array();
        foreach ($steps as $step) {
            if ($step == $routeStep) {
                if ($state == 'disabled') {
                    Flash::error('Not Allowed');
                    return Redirect::action('LoanApplicationController@getInstructions');
                }
                $class = 'active';
                $state ='disabled';
            }
            if ($step == $currentStep && $state != 'disabled') {
                $class = 'active';
                $state ='disabled';
            } elseif ($step != $routeStep) {
                $class = $state;
            }
            $this->steps[$step] = ['class' => $class];
        }
        
        $this->setCurrentStep($routeStep);
    }
    
    protected function setCurrentStep($step)
    {
        Session::set('loanapplication.currentStep', $step);
    }
    
    protected function stepView($step)
    {
        return View::make("borrower.loan.$step", ['steps' => $this->steps]);    
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
        return $this->stepView('profile');
    }
}
