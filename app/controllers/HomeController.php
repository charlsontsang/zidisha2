<?php

class HomeController extends BaseController {


    public function getHome()
    {
        return $this->getLenderHome();
    }
    
	public function getLenderHome()
	{
		return View::make('lender-home');
	}

}
