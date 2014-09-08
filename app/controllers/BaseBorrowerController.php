<?php


use Zidisha\Borrower\Borrower;

class BaseBorrowerController extends BaseController
{

    /**
     * @return Borrower
     */
    protected function getBorrower()
    {
        return \Auth::user()->getBorrower();
    }
    
}
