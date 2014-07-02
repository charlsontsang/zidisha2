<?php

namespace Zidisha\Borrower;

use Zidisha\Borrower\Base\Borrower as BaseBorrower;

class Borrower extends BaseBorrower
{

    const PAYMENT_COMPLETE = 1;
    const PAYMENT_INCOMPLETE = 2;
    const PAYMENT_PROCESSED = 3;
    const PAYMENT_FAILED = 4;
    const PAYMENT_DELETED = 5;

    public function getName(){
        return $this->getFirstName() . " " . $this->getLastName();
    }
    
    public function getCommunityLeader()
    {
        foreach ($this->getContacts() as $contact) {
            if ($contact->getType() == 'communityLeader') {
                return $contact;
            }
        }
        
        return null;
    }

}
