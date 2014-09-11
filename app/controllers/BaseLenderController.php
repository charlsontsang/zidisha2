<?php


use Zidisha\Lender\Lender;

class BaseLenderController extends BaseController
{

    /**
     * @return Lender
     */
    protected function getLender()
    {
        return \Auth::user()->getLender();
    }
    
}
