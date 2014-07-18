<?php

return [
    'form' => [
        'username'              => 'Display Username',
        'email'                 => 'Email',
        'password'              => 'Password',
        'password-confirmation' => 'Confirm Password',
        'submit'                => 'Submit',
        'country-id'            => 'Country'
    ],

    'validation' => [
        'facebook-account-exists' => 'This facebook account already linked with another
                account on our website.',
        'facebook-email-exists'   => 'The email address linked to the facebook
                account is already linked with another account on our website.',
        'google-account-exists'   => 'This google account already linked with another account on our website.',
        'google-email-exists'     => 'The email address linked to the google account is already linked with another account on
                our website.',
    ],

    'flash' => [
        'oops'                          => 'Oops, something went wrong',
        'facebook-no-account-connected' => 'No Facebook account connected.',
        'google-no-account-connected'   => 'No Google account connected.',
    ],
];
