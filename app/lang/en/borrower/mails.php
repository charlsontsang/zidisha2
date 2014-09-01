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
];
