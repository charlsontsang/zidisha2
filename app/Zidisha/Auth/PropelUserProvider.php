<?php

namespace Zidisha\Auth;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\UserProviderInterface;
use Zidisha\User\User;
use Zidisha\User\UserQuery;

class PropelUserProvider implements UserProviderInterface
{

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Auth\UserInterface|null
     */
    public function retrieveById($identifier)
    {
        return UserQuery::create()->findOneById($identifier);
    }

    /**
     * Retrieve a user by by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return \Illuminate\Auth\UserInterface|null
     */
    public function retrieveByToken($identifier, $token)
    {
        return UserQuery::create()
            ->filterById($identifier)
            ->filterByRememberToken($token)
            ->findOne();
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Auth\UserInterface $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(UserInterface $user, $token)
    {
        $user->setRememberToken($token)->save();
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Auth\UserInterface|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        return UserQuery::create()
            ->filterByEmail($credentials['email'])
            ->filterByActive(true) // because of duplicate email addresses
            ->findOne();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Auth\UserInterface $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(UserInterface $user, array $credentials)
    {
        /** @var User $user */
        $check = \Hash::check($credentials['password'], $user->getPassword());

        if (!$check) {
            // Try with old password hashing scheme
            if ($user->getSalt()) {
                $password = md5(md5($credentials['password']) . md5($user->getSalt()));
            } else {
                $password = md5($credentials['password']);
            }

            $check = $user->getPassword() == $password;

            if ($check) {
                $user
                    ->setSalt(null)
                    ->setPassword($credentials['password']);
                $user->save();
            }
        }

        return $check;
    }
}
