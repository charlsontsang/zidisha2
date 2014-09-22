<?php

// Don't add the "required" validation rule!

return [
    'Site' => [
        'site.replyToEmailAddress' => [
            'label'   => 'Reply to email address',
            'default' => 'service@zidisha.org',
            'rule'    => 'email',
        ],
        'site.fromEmailAddress' => [
            'label'   => 'From email address',
            'default' => 'service@zidisha.org',
            'rule'    => 'email',
        ],
        'site.adminId' => [
            'default' => 1,
            'label' => 'Admin ID',
            'rule'  => 'numeric',
        ],
        'site.YCAccountId' => [
            'default' => 2,
            'label' => 'YC account ID',
            'rule'  => 'numeric',
        ],
        'site.countriesCodesBlockedFromUploadFunds' =>[
            'default' => 'CN,ID,SG',
            'label'   => 'Blocked countries',
            'rule'    => ['regex:/^[A-Z,]+$/'],
        ],
        'site.paymentTransactionFeeRate' =>[
            'label'   => 'Transaction fee rate',
            'default' => '3.5',
            'rule'    => 'numeric',
        ],
    ],
    'Loan' => [
        'loan.minimumAmount' => [
            'label'   => 'Minimum loan amount',
            'default' => '10',
            'rule'    => 'numeric',
            'prepend' => '$',
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
            'label'   => 'Number of days after which an unfunded loan application will expire automatically',
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
        'loan.secondLoanPercentage' => [
            'label' => 'Credit limit increase after on-time repayment of loans up to $200 (% of largest previous loan)',
            'default' => '300',
            'rule' => 'numeric'
        ],
        'loan.nextLoanPercentage' => [
            'label' => 'Credit limit increase after on-time repayment of loans over $200 (% of largest previous loan)',
            'default' => '150',
            'rule' => 'numeric'
        ],
        'loan.firstLoanValue' => [
            'label' => 'Maximum amount for first loan (without bonus)',
            'default' => '50',
            'rule' => 'numeric'
        ],
        'loan.nextLoanValue' => [
            'label' => 'Maximum amount for subsequent loans',
            'default' => '10000',
            'rule' => 'numeric'
        ],
        'loan.repaymentReminderDay' => [
            'label' => 'Number of past due days after which a repayment reminder will be sent',
            'default' => '14',
            'rule' => 'numeric'
        ],
        'loan.repaymentDueAmount' => [
            'label' => 'Loan past due threshold amount',
            'default' => '5',
            'rule' => 'numeric'
        ],
        'loan.deadline' => [
            'label' => 'Loan deadline date',
            'default' => '15',
            'rule' => 'numeric'
        ],
        'loan.maxExtraPeriodRescheduledLoan' => [
            'label' => 'Maximum repayment period for rescheduled loans (from the date of rescheduling)',
            'default' => '60',
            'rule' => 'numeric'
        ],
        'loan.maxRescheduleAllowed' => [
            'label' => 'Maximum reschedulement allowed for a loan',
            'default' => '1000',
            'rule' => 'numeric'
        ],
    ],
    'Borrower' => [
        'invite.bonus' => [
            'label'   => 'Bonus for new members who were invited by eligible existing members',
            'default' => '100',
            'rule'    => 'numeric',
        ],
        'invite.maxInviteesWithoutPayment' => [
            'label'   => 'Maximum invitees without payments',
            'default' => '3',
            'rule'    => 'numeric',
        ],
        'invite.minRepaymentRate' => [
            'label'   => 'Minimum repayment rate to send invites',
            'default' => '95',
            'rule'    => 'numeric',
        ],
        'facebook.minimumFriends' => [
            'label'   => 'Minimum Facebook friends required',
            'default' => '20',
            'rule'    => 'numeric',
        ],
        'facebook.minimumMonths' => [
            'label'   => 'Minimum age of Facebook account (months)',
            'default' => '3',
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
            'label' => 'Sift Science API Key'
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
            'label' => 'Sendwithus API Key'
        ],
        'telerivet.apiKey' => [
            'label' => 'Telerivet API Key'
        ],
        'telerivet.projectId' => [
            'label' => 'Telerivet Project ID'
        ]
    ],
    'Sendwithus' => [
        'sendwithus.introduction-template-id' => [
            'label' => 'Introduction template ID'
        ],
        'sendwithus.lender-expired-loan-template-id' => [
            'label' => 'Loan expired notification template ID'
        ],
        'sendwithus.borrower-notifications-template-id' => [
            'label' => 'Borrower account notifications template ID'
        ],
        'sendwithus.lender-unused-funds-template-id' => [
            'label' => 'Lender unused funds notification template ID'
        ],
        'sendwithus.loan-about-to-expire-mail-template-id' => [
            'label' => 'Loan about to expire mail template ID'
        ],
        'sendwithus.borrower-again-repayment-instruction-template-id' => [
            'label' => 'Borrower repayment instructions template ID'
        ],
        'sendwithus.lender-loan-forgiveness-mail-template-id' => [
            'label' => 'Lender loan forgiveness template ID'
        ],
        'sendwithus.lender-loan-first-bid-confirmation-template-id' => [
            'label' => 'Lender loan first bid confirmation template ID'
        ],
        'sendwithus.lender-account-abandoned-template-id' => [
            'label' => 'Lender account abandoned template ID'
        ],
        'sendwithus.lender-invite-credit-template-id' => [
            'label' => 'Lender invite credit template ID'            
        ],
        'sendwithus.lender-loan-fully-funded-template-id' => [
            'label' => 'Lender loan fully funded template ID'            
        ],
        'sendwithus.lender-loan-disbursed-template-id' => [
            'label' => 'Lender loan disbursed template ID'
        ],
        'sendwithus.lender-loan-repayment-template-id' => [
            'label' => 'Lender loan repayment template ID'
        ],
        'sendwithus.borrower-invite-template-id' => [
            'label' => 'Borrower invite template ID'
        ],
        'sendwithus.comments-borrower-template-id' => [
            'label' => 'Comments borrower template ID'
        ],
        'sendwithus.comments-template-id' => [
            'label' => 'Comments template ID'
        ],
        'sendwithus.inactive-invitee-template-id' => [
            'label' => 'Inactive invitee template ID'
        ],
    ]
];
