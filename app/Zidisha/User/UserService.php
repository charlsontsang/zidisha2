<?php

namespace Zidisha\User;


use Zidisha\Lender\Lender;
use Zidisha\Lender\Profile;
use Zidisha\Mail\LenderMailer;

class UserService
{

    /**
     * @var UserQuery
     */
    private $userQuery;
    private $lenderMailer;

    public function __construct(UserQuery $userQuery, LenderMailer $lenderMailer)
    {
        $this->userQuery = $userQuery;
        $this->lenderMailer = $lenderMailer;
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
            ->setFacebookId($facebookUser['id']);

        $lender = new Lender();
        $lender
            ->setUser($user)
            ->setFirstName($facebookUser['first_name'])
            ->setLastName($facebookUser['last_name'])
            // TODO
            //->setCountry($facebookUser['location']);
            ->setCountryId(1);

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

}