<?php

return [
    'loan-confirmation' => [
        'subject' => 'Your Loan Application Has Been Published',
        'body'    => 'Dear :borrowerName,<br/><br/>
Congratulations!  Your loan application has been posted for funding.  Click <a href=":loanApplicationPage">here</a> to view your loan application page.<br/><br/>
Please note that your application will be posted for a maximum of :loanApplicationDeadLine days, or until it is fully funded and you choose to accept the bids raised. You may edit your loan application page at any time using the <a href=":loanApplicationLink">Loan Application</a> page.<br/><br/>
Best of luck in your endeavor,<br/><br/>
The Zidisha Team'
    ],
    'loan-fully-funded' => [
        'subject' => 'Loan funding confirmation email',
        'body'    => 'Dear :borrowerName, <br/><br/>
Congratulations!  Your loan application is fully funded.<br/><br/>
You may accept the loan bids and receive the loan disbursement at any time before your application expires on :expiryDate. <br/><br/>
To accept the bids, please go to your <a href=":loanApplicationPage">loan application page</a> and log in to your member account.Then click on the "Accept Bids" button in your loan profile page.<br/><br/>
Please do not hesitate to contact us at service@zidisha.org if you desire assistance.
<br/><br/>
Best wishes,<br/><br/>
The Zidisha Team'
    ],
    'loan-disbursed' => [
        'subject' => 'Loan disbursement confirmation',
        'body' => 'Dear :borrowerName ,<br/><br/>This is to confirm disbursement of your Zidisha loan in the amount of :disbursedAmount. If this is your first Zidisha loan, the new client registration fee of :registrationFee was deducted from your loan disbursement for a net payment of :netAmount.<br/><br/>".
"To view your repayment schedule please log into your account at <a href=":zidishaLink" target=\'_blank\'>www.zidisha.org</a> and click on "Repayment Schedule"<br/><br/>'.
':repaymentInstruction<br/><br/>'.
'Now, we\'d like to ask for your help. Please log in to your account at <a href=":zidishaLink" target=\'_blank\'>www.zidisha.org</a> and click "Post a Comment". Then type a comment to let lenders know exactly what you have been able to purchase with the loan, and how it has helped you.  Regular communication with lenders regarding the results of their loan will help establish good relations such that that they will be happy to lend to you again in the future.<br/><br/>'.
'Should you have any questions, please do not hesitate to contact us at service@zidisha.org.<br/><br/>'.
'We wish you much success in your endeavor.<br/><br/>'.
'Zidisha Team'
    ],
];
