<?php

namespace Zidisha\Borrower;


use Zidisha\User\User;
use Zidisha\Vendor\PropelDB;

class BorrowerActivationService
{
    public function review(Borrower $borrower, User $user, $data)
    {
        $review = ReviewQuery::create()
            ->filterByBorrower($borrower)
            ->findOne();
        
        if (!$review) {
            $review = new Review();
            $review
                ->setBorrower($borrower)
                ->setCreatedBy($user->getId());
        } else {
            $review->setModifiedBy($user->getId());
        }
                
        $review
            ->setIsAddressLocatable($data['isAddressLocatable'])
            ->setIsAddressLocatableNote($data['isAddressLocatableNote']);
        
        PropelDB::transaction(function() use ($review, $borrower) {
            $review->save();
            if ($review->isCompleted()) {
//                $borrower->setActivationStatus('pending-verification'); TODO
            }
        });
        
        return $review;
    }
}
