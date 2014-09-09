<?php

namespace Zidisha\Lender;

use Zidisha\Borrower\Borrower;
use Zidisha\Lender\Base\Lender as BaseLender;

class Lender extends BaseLender
{
    public function getName(){
        return $this->getFirstName() . " " . $this->getLastName();
    }

    public function isActive(){
        return $this->getActive();
    }
}
