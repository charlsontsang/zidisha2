<?php

namespace Zidisha\User;


use Zidisha\Lender\Lender;

class UserService
{

    /**
     * @var UserQuery
     */
    private $userQuery;

    public function __construct(UserQuery $userQuery)
    {
        $this->userQuery = $userQuery;
    }
    
    public function joinUser($data)
    {
        $user = new User();
        $user->setPassword($data['password']);
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        
        return $user->save() ? $user : false;
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
            ->setAboutMe($data['aboutMe'])
            // TODO
            //->setCountry($facebookUser['location']);
            ->setCountryId(1);

        $lender->save();

        Auth::loginUsingId($user->getId());
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