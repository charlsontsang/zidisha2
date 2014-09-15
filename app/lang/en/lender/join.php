<?php

return [
    'form' => [
        'username'              => 'Create display name',
        'email'                 => 'Your email',
        'password'              => 'Create password',
        'submit'                => 'Join',
        'country-id'            => 'Your country'
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
        'facebook-no-account-connected' => 'Please connect your Facebook account to continue.',
        'google-no-account-connected'   => 'Please connect your Google account to continue.',
    ],
];
