<?php
namespace Zidisha\Lender;


use Zidisha\Upload\Upload;
use Zidisha\User\User;

class LenderService
{
    public function editProfile(Lender $lender, $data)
    {
        $lender->setFirstName($data['firstName']);
        $lender->setLastName($data['lastName']);
        $lender->getUser()->setEmail($data['email']);
        $lender->getUser()->setUsername($data['username']);
        $lender->getProfile()->setAboutMe($data['aboutMe']);

        if (!empty($data['password'])) {
            $lender->getUser()->setPassword($data['password']);

        }

        $lender->save();
    }

    public function uploadPicture(Lender $lender, $image)
    {
        $user = $lender->getUser();

        if ($image) {
            $upload = Upload::createFromFile($image);
            $upload->setUser($user);

            $user->setProfilePicture($upload);
            $user->save();
        }
    }
}
