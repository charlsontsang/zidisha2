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

    public function editBorrower(Borrower $borrower, $data)
    {
        $borrower->setFirstName($data['firstName']);
        $borrower->setLastName($data['lastName']);
        $borrower->getUser()->setEmail($data['email']);
        $borrower->getUser()->setUsername($data['username']);
        $borrower->getProfile()->setAboutMe($data['aboutMe']);
        $borrower->getProfile()->setAboutBusiness($data['aboutBusiness']);

        if (!empty($data['password'])) {
            $borrower->getUser()->setPassword($data['password']);
        }

        $borrower->save();
    }

    public function uploadPicture(Borrower $borrower, $image)
    {
        $user = $borrower->getUser();

        if ($image) {
            $upload = Upload::createFromFile($image);
            $upload->setUser($user);

            $user->setProfilePicture($upload);
            $user->save();
        }
    }
} 