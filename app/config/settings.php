<?php

// Don't add the "required" validation rule!

return [
    'Site' => [
        'site.replyTo' => [
            'label' => 'Reply To',
            'default' => 'service@zidisha.org',
            'rule' => 'email',
        ],
    ],
    'API' => [
        'facebook.appId' => [
            'label' => 'Facebook App ID',
        ],
        'facebook.appSecret' => [
            'label' => 'Facebook App Secret',
        ],
        'mixpanel.token' => [
            'label' => 'Mixpanel Token',
        ],
        'stripe.publicKey' => [
            'label' => 'Stripe Public Key',
        ],
        'stripe.secretKey' => [
            'label' => 'Stripe Secret Key',
        ]
    ],
];
