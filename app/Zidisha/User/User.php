<?php

namespace Zidisha\User;

use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Auth\UserInterface;
use Propel\Runtime\Connection\ConnectionInterface;
use Zidisha\User\Base\User as BaseUser;
use Zidisha\User\Exceptions\InvalidUserRoleException;

class User extends BaseUser implements UserInterface, RemindableInterface
{
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
        if ($this->getRole() != 'lender') {
            throw new InvalidUserRoleException;
        }

        return parent::getLender($con);
    }

    public function getBorrower(ConnectionInterface $con = null)
    {
        if ($this->getRole() != 'borrower') {
            throw new InvalidUserRoleException;
        }

        return parent::getBorrower($con);
    }

    public function getProfilePictureUrl($format = 'small-profile-picture')
    {
        if ($this->hasProfilePicture()) {
            return $this->getProfilePicture()->getImageUrl($format);
        }

        return $this->getDefaultPicture();
    }

    public function hasProfilePicture()
    {
        return $this->getProfilePicture() ? true : false;
    }

    public function getDefaultPicture()
    {
        return asset('/assets/images/default.jpg');
    }

    public function getProfileUrl($parameters = [])
    {
        $route = $this->getRole() . ":public-profile";

        return route($route, ['username' => $this->getUsername()] + $parameters);
    }

    public function isLender()
    {
        return $this->getRole() == 'lender';
    }

    public function isBorrower()
    {
        return $this->getRole() == 'borrower';
    }

    public function isAdmin()
    {
        return $this->getRole() == 'admin';
    }

    public function isVolunteer()
    {
        return $this->isLender() && $this->getSubRole() == 'volunteer';
    }

    public function isVolunteerMentor()
    {
        return $this->isBorrower() && $this->getSubRole() == 'volunteerMentor';
    }

}
