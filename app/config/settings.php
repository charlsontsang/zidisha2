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
            'default' => 1,
            'label' => 'Admin id',
            'rule'  => 'numeric',
        ],
        'site.YCAccountId' => [
            'default' => 2,
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
        'loan.expireThreshold' => [
            'label'   => 'Number of days after which an unfunded loan application will expire automatically.',
            'default' => '14',
            'rule'    => 'numeric'
        ],
        'loan.maximumLenderInterestRate' => [
            'label'   => 'Maximum lender interest rate (% of loan principal per annum)',
            'default' => '15',
            'rule'    => 'numeric'
        ],
        'loan.transactionFeeRate' => [
            'label'   => 'Borrower transaction fee rate (% of loan principal per annum)',
            'default' => '5',
            'rule'    => 'numeric'
        ],
        'loan.loanIncreaseThresholdLow' => [
            'label'   => 'Number of months between loan size increases (below $200)',
            'default' => '1',
            'rule'    => 'numeric'
        ],
        'loan.loanIncreaseThresholdMid' => [
            'label'   => 'Number of months between loan size increases ($200 - $1000)',
            'default' => '3',
            'rule'    => 'numeric'
        ],
        'loan.loanIncreaseThresholdHigh' => [
            'label'   => 'Number of months between loan size increases ($1000 - $3000)',
            'default' => '6',
            'rule'    => 'numeric'
        ],
        'loan.loanIncreaseThresholdTop' => [
            'label'   => 'Number of months between loan size increases (above $3000)',
            'default' => '12',
            'rule'    => 'numeric'
        ],
    ],
    'Borrower' => [
        'invite.maxInviteesWithoutPayment' => [
            'label'   => 'Maximum Invites Without Payments',
            'default' => '3',
            'rule'    => 'numeric',
        ],
        'invite.minRepaymentRate' => [
            'label'   => 'Minimum Repayment Rate to send Invites',
            'default' => '95',
            'rule'    => 'numeric',
        ]
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
        'sendwithus.lender-expired-loan-template-id' => [
            'label' => 'Loan expired notification'
        ],
        'sendwithus.borrower-notifications-template-id' => [
            'label' => 'Borrower account notifications'
        ]
    ]
];
