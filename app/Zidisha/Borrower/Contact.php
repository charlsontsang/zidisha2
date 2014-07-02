<?php

namespace Zidisha\Borrower;

use Zidisha\Borrower\Base\Contact as BaseContact;

class Contact extends BaseContact
{

    public function getName(){
        return $this->getFirstName() . " " . $this->getLastName();
    }
    
}
