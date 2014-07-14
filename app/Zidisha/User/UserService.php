<?php

namespace Zidisha\User;

use Zidisha\Lender\Lender;
use Zidisha\Lender\LenderService;
use Zidisha\Lender\Profile;
use Zidisha\Mail\LenderMailer;

class UserService
{

    private $userQuery;
    private $lenderMailer;
    private $lenderService;

    public function __construct(UserQuery $userQuery, LenderMailer $lenderMailer, LenderService $lenderService)
    {
        $this->userQuery = $userQuery;
        $this->lenderMailer = $lenderMailer;
        $this->lenderService = $lenderService;
    }
    
    public function joinUser($data)
    {

        $user = new User();
        $user->setPassword($data['password']);
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setRole('lender');
        
        $lender = new Lender();
        $lender
            ->setUser($user)
            ->setCountryId($data['countryId']);

        $profile = new Profile();
        $lender->setProfile($profile);
        $lender->save();

        $this->lenderMailer->sendLenderIntroMail($lender);

        return $lender;
    }
    
    public function joinFacebookUser($facebookUser, $data)
    {
        $user = new User();
        $user
            ->setUsername($data['username'])
            ->setEmail($facebookUser['email'])
            ->setRole("lender")
            ->setFacebookId($facebookUser['id']);

        $lender = new Lender();
        $lender
            ->setUser($user)
            ->setFirstName($facebookUser['first_name'])
            ->setLastName($facebookUser['last_name'])
            ->setCountryId($data['countryId']);

        $profile = new Profile();
        $profile->setAboutMe($data['aboutMe']);
        $lender->setProfile($profile);

        $lender->save();

        return $lender;
    }

    public function validateConnectingFacebookUser($facebookUser)
    {
        $checkUser = $this->userQuery
            ->filterByFacebookId($facebookUser['id'])
            ->_or()
            ->filterByEmail($facebookUser['email'])
            ->findOne();
        
        $errors = array();
        if ($checkUser) {
            if ($checkUser->getFacebookId() == $facebookUser['id']) {
                $errors[] = 'This facebook account already linked with another account on our website.';
            } else {
                $errors[] = 'The email address linked to the facebook account is already linked with another account on our website.';
            }
        }

        return $errors;
    }

    public function joinGoogleUser(\Google_Service_Oauth2_Userinfoplus $googleUser, $data)
    {
        $user = new User();
        $user
            ->setUsername($data['username'])
            ->setEmail($googleUser->getEmail())
            ->setRole("lender")
            ->setGoogleId($googleUser->getId())
            ->setGooglePicture($googleUser->getPicture());

        $lender = new Lender();
        $lender
            ->setUser($user)
            ->setFirstName($googleUser->getGivenName())
            ->setLastName($googleUser->getFamilyName())
            ->setCountryId($data['countryId']);

        $profile = new Profile();
        $profile->setAboutMe($data['aboutMe']);
        $lender->setProfile($profile);

        $lender->save();

        return $lender;
    }

    public function validateConnectingGoogleUser(\Google_Service_Oauth2_Userinfoplus $googleUser)
    {
        $checkUser = $this->userQuery
            ->filterByGoogleId($googleUser->getId())
            ->_or()
            ->filterByEmail($googleUser->getEmail())
            ->findOne();

        $errors = array();
        if ($checkUser) {
            if ($checkUser->getGoogleId() == $googleUser->getId()) {
                $errors[] = 'This google account already linked with another account on our website.';
            } else {
                $errors[] = 'The email address linked to the google account is already linked with another account on our website.';
            }
        }

        return $errors;
    }

}