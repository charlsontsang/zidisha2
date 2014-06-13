<?php
namespace Zidisha\Borrower;

class BorrowerService
{
    public function joinBorrower($data)
    {
        $user = new \Zidisha\User\User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setFacebookId($data['facebook_id']);
        $user->setRole('borrower');

        $borrower = new Borrower();
        $borrower->setFirstName($data['first_name']);
        $borrower->setLastName($data['last_name']);
        $borrower->setCountryId($data['country']);
        $borrower->setUser($user);

        $profile = new Profile();
        $borrower->setProfile($profile);

        $borrower->save();

        return $borrower;
    }
} 