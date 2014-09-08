<?php

use Illuminate\Routing\Controller;
use Zidisha\User\User;

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

    protected function flash(){

    }

    /**
     * @return User
     */
    protected function getUser()
    {
        return \Auth::user();
    }
}
