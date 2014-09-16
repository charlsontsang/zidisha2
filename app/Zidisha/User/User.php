<?php

namespace Zidisha\User;

use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Auth\UserInterface;
use Propel\Runtime\Connection\ConnectionInterface;
use Zidisha\User\Base\User as BaseUser;
use Zidisha\User\Exceptions\InvalidUserRoleException;

class User extends BaseUser implements UserInterface, RemindableInterface
{
    const LENDER_ROLE_ENUM = 0;
    const BORROWER_ROLE_ENUM = 1;
    const PARTNER_ROLE_ENUM = 2;
    const ADMIN_ROLE_ENUM = 3;

    const ROLE_LENDER   = 'lender';
    const ROLE_BORROWER = 'borrower';
    const ROLE_ADMIN    = 'admin';

    const SUB_ROLE_VOLUNTEER        = 'volunteer';
    const SUB_ROLE_VOLUNTEER_MENTOR = 'volunteerMentor';


    public function setPassword($password)
    {
        return parent::setPassword(\Hash::make($password));
    }

    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getId();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->getPassword();
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return $this->getRememberToken();
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->getEmail();
    }

    public function getLender(ConnectionInterface $con = null)
    {
        if ($this->getRole() != User::ROLE_LENDER) {
            throw new InvalidUserRoleException;
        }

        return parent::getLender($con);
    }

    public function getBorrower(ConnectionInterface $con = null)
    {
        if ($this->getRole() != User::ROLE_BORROWER) {
            throw new InvalidUserRoleException;
        }

        return parent::getBorrower($con);
    }

    public function getProfilePictureUrl($format = 'small-profile-picture')
    {
        if ($this->hasProfilePicture()) {
            return $this->getProfilePicture()->getImageUrl($format);
        }elseif ($this->getFacebookId()) {
           $width =  \Config::get("image.formats.$format.width");
           $height = \Config::get("image.formats.$format.height");
            return 'https://graph.facebook.com/'.$this->getFacebookId().'/picture?width='.$width.'&height='.$height;
        }elseif ($this->getGoogleId()) {
            $width =  \Config::get("image.formats.$format.width");
            return  ($this->getGooglePicture(). '?sz='.$width);
        }

        return $this->getDefaultPicture();
    }

    public function hasProfilePicture()
    {
        return $this->getProfilePicture() ? true : false;
    }

    public function getDefaultPicture()
    {
        $pictures = array('1', '2', '3', '4', '5', '6');
        $picture = $pictures[array_rand($pictures)];
        return asset('/assets/images/profile-default/profile-default'.$picture.'.jpg');
    }

    public function getProfileUrl($parameters = [])
    {
        $route = $this->getRole() . ":public-profile";

        return route($route, ['username' => $this->getUsername()] + $parameters);
    }

    public function isLender()
    {
        return $this->getRole() == User::ROLE_LENDER;
    }

    public function isBorrower()
    {
        return $this->getRole() == User::ROLE_BORROWER;
    }

    public function isAdmin()
    {
        return $this->getRole() == User::ROLE_ADMIN;
    }

    public function isVolunteer()
    {
        return $this->isLender() && $this->getSubRole() == User::SUB_ROLE_VOLUNTEER;
    }

    public function isVolunteerMentor()
    {
        return $this->isBorrower() && $this->getSubRole() == User::SUB_ROLE_VOLUNTEER_MENTOR;
    }

    public function getSubObject()
    {
        if ($this->isLender()) {
            return $this->getLender();
        } elseif ($this->isBorrower()) {
            return $this->getBorrower();
        }
        return null;
    }

    public function isVolunteerOrAdmin()
    {
        return ($this->isVolunteer() || $this->isAdmin());
    }
}
