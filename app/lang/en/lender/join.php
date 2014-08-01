<?php

return [
    'form' => [
        'username'              => 'Create a display name',
        'email'                 => 'Your email',
        'password'              => 'Password',
        'password-confirmation' => 'Confirm Password',
        'submit'                => 'Join',
        'country-id'            => 'Country'
    ],

    'validation' => [
        'facebook-account-exists' => 'This Facebook account is already linked to another
                Zidisha account.',
        'facebook-email-exists'   => 'This Facebook account\'s email address is already linked to another
                Zidisha account.',
        'google-account-exists'   => 'This Google account is already linked to another
                Zidisha account.',
        'google-email-exists'     => 'This Google address is already linked to another
                Zidisha account.',
    ],

    'flash' => [
        'oops'                          => 'Oops, something went wrong',
        'facebook-no-account-connected' => 'Please connect your Facebook account to continue.',
        'google-no-account-connected'   => 'Please connect your Google account to continue.',
    ],
];
