<?php

// Don't add the "required" validation rule!

return [
    'Site' => [
        'site.replyToEmailAddress' => [
            'label'   => 'Reply To Email Address',
            'default' => 'service@zidisha.org',
            'rule'    => 'email',
        ],
        'site.fromEmailAddress' => [
            'label'   => 'Reply To Email Address',
            'default' => 'service@zidisha.org',
            'rule'    => 'email',
        ],
        'site.adminId' => [
            'label' => 'Admin id',
            'rule'  => 'numeric',
        ],
        'site.YCAccountId' => [
            'label' => 'YCAccount id',
            'rule'  => 'numeric',
        ],
        'site.countriesCodesBlockedFromUploadFunds' =>[
            'default' => 'CN,ID,SG',
            'label'   => 'Countries Codes Blocked From Upload Funds',
            'rule'    => ['regex:/^[A-Z,]+$/'],
        ],
        'site.paymentTransactionFeeRate' =>[
            'label'   => 'Payment Transaction Fee Rate',
            'default' => '3.5',
            'rule'    => 'numeric',
        ],
    ],
    'Loan' => [
        'loan.minimumAmount' => [
            'label'   => 'Minimum loan amount',
            'default' => '10',
            'rule'    => 'numeric',
            'prepend' => 'USD',
        ],
        'loan.maximumPeriod' => [
            'label'   => 'Maximum loan period for applications (months)',
            'default' => '24',
            'rule'    => 'numeric',
        ],
        'loan.serviceFeeRate' => [
            'label'   => 'Borrower transaction fee rate (% of loan principal per annum)',
            'default' => '5',
            'rule'    => 'numeric',
        ],
    ],
    'API' => [
        'facebook.appId' => [
            'label' => 'Facebook App ID',
        ],
        'facebook.appSecret' => [
            'label' => 'Facebook App Secret',
        ],
        'google.clientId' => [
            'label' => 'Google Client ID',
        ],
        'google.clientSecret' => [
            'label' => 'Google Client Secret',
        ],
        'mixpanel.token' => [
            'label' => 'Mixpanel Token',
        ],
        'stripe.publicKey' => [
            'label' => 'Stripe Public Key',
        ],
        'stripe.secretKey' => [
            'label' => 'Stripe Secret Key',
        ],
        'sift-science.api-key' => [
            'label' => 'Sift Science Api Key'
        ],
        'paypal.mode' => [
            'label' => 'PayPal Process Mode'
        ],
        'paypal.username' => [
            'label' => 'PayPal Username'
        ],
        'paypal.password' => [
            'label' => 'PayPal Password'
        ],
        'paypal.signature' => [
            'label' => 'PayPal Signature'
        ],
        'sendwithus.apiKey' => [
            'label' => 'Sendwithus api key'
        ],
        'telerivet.apiKey' => [
            'label' => 'Telerivet Api Key'
        ],
        'telerivet.projectId' => [
            'label' => 'Telerivet Project Id'
        ]
    ],
    'Sendwithus' => [
        'sendwithus.introduction-template-id' => [
            'label' => 'Introduction template id'
        ],
    ]
];
