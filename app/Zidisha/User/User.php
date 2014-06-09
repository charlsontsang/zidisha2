<?php

namespace Zidisha\User;

use Zidisha\User\Base\User as BaseUser;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Auth\UserInterface;
use Propel\Runtime\Connection\ConnectionInterface;

class User extends BaseUser implements UserInterface, RemindableInterface
{
    public function setPassword($password)
    {
        return parent::setPassword(\Hash::make($password));
    }

    public function preSave(ConnectionInterface $con = null)
    {
        $this->setLastLoginAt(new \DateTime());
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
}
