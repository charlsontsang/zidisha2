<?php

use Illuminate\Routing\Route;

trait StepController
{
    protected $stepsData = [];
    protected $stepsSessionKey = '';
    
    protected function getStepFromRoute(Route $route)
    {
        $controllerMethod = $route->getAction()['controller'];
        list($controller, $method) = explode('@', $controllerMethod);

        return preg_replace('/^(get|post)_/', '', snake_case($method));
    }

    protected function stepSessionKey(Route $route)
    {
        if (!$this->stepsSessionKey) {
            $controller = get_class();
            $controller = substr($controller, 0, strlen($controller) - 10);

            $this->stepsSessionKey = str_replace('_', '-', snake_case($controller));
        }
    }
    
    protected function getStepsSession($key, $default = null) {
        return Session::get($this->stepsSessionKey . '.' . $key, $default);
    }

    protected function putStepsSession($key, $value) {
        return Session::put($this->stepsSessionKey . '.' . $key, $value);
    }

    protected function flushStepsSession() {
        return Session::forget($this->stepsSessionKey);
    }

    protected function getCurrentStep() {
        return $this->getStepsSession('currentStep', $this->steps[0]);
    }

    protected function setCurrentStep($step)
    {
        $this->putStepsSession('currentStep', $step);
    }
    
    protected function stepNotAllowed() {
        $method = camel_case('get_' . $this->steps[0]);
        return Redirect::action(get_class() . '@' . $method);
    }

    public function stepsBeforeFilter(Route $route)
    {
        $routeStep = $this->getStepFromRoute($route);
        $this->stepSessionKey($route);
        
        $currentStep = $this->getCurrentStep();
        
        $state = 'complete';
        $this->stepsData = [];
        foreach ($this->steps as $step) {
            if ($step == $routeStep) {
                if ($state == 'disabled') {
                    return $this->stepNotAllowed();
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
            $this->stepsData[$step] = [
                'class' => $class,
                'action' => get_class() . '@get' . ucfirst(camel_case($step))
            ];
        }

        $this->setCurrentStep($routeStep);
    }

    public function isNewLoanAllowedFilter()
    {
        $borrower = \Auth::user()->getBorrower();

        if (!$borrower->isNewLoanAllowed()) {
            \Flash::error('You are not allowed to make new loan right now.');
            return Redirect::route('borrower:dashboard');
        }
    }
}
