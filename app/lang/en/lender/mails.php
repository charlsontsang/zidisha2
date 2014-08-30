<?php
return [
    'new-loan-notification' => [
        'subject' => ':borrowerName has posted a new loan application!',
        'lender-body' => ':borrowerName fully repaid the loan you funded on :repayDate, and has just posted a new loan application!
<br/><br/>
You may view :borrowerName\'s current loan request at <a href=":loanUrl">:loanUrl</a> 
<br/><br/>
Cheers,
<br/><br/>
The Zidisha Team',

        'follower-body' => '
One of the entrepreneurs you are following, :borrowerName, has just posted a new loan application!
<br/><br/>
You may view :borrowerName \'s current loan request at <a href=":loanUrl">:loanUrl</a> 
<br/><br/>
Cheers,
<br/><br/>
The Zidisha Team'
    ],
    'loan-fully-funded' => [
        'subject' => 'Loan funding confirmation email',
        'body'    => 'Dear :borrowerName, 
                    <br/><br/>
                    Congratulations!  Your loan application is fully funded.
                    <br/><br/>
                    You may accept the loan bids and receive the loan disbursement at any time before your application expires on :applicationExpiryDate. 
                    <br/><br/>
                    To accept the bids, please go to your <a href=":loanApplicationLink">loan application page</a> and log in to your member account.Then click on the "Accept Bids" button in your loan profile page.
                    <br/><br/>
                    Please do not hesitate to contact us at service@zidisha.org if you desire assistance.
                    <br/><br/>
                    Best wishes,
                    <br/><br/>
                    The Zidisha Team'
    ],
    'lender-invite' => [
        'subject' => ':lenderName has sent you $25 to lend to a Zidisha entrepreneur',
        'body'    => '
:lenderName has sent you $25 to lend to a Zidisha entrepreneur.
<br/>
:customMessage
Use your $25 to fund a small business growth loan to a disadvantaged entrepreneur in Africa or Asia.
You can communicate with your chosen entrepreneur via the Zidisha website as his or her business develops - changing a life while interacting directly with a remarkable individual on the other side of the world.'
    ],
    
    'lender-unused-fund' => [
        'subject' => 'You have unused funds in your Zidisha account',
        'body'    => 'Hi there,
                     <br/><br/>
                     Since you last visited Zidisha, repayments from entrepreneurs you\'ve supported have increased your lending account balance to USD :lenderBalance.
                     <br/><br/>
                     We know you’re busy, and wanted to help you out by picking out three amazing loan projects you can support right now:',
        'extra' => 'Happy lending,
                            <br/><br/>
                            The Zidisha Team
                            <br/><br/>
                            PS:  Use our automated lending tool to continuously reinvest your repayments in new loans!  Set it up <a href=":automaticLendingLink">here</a>.',
        'header' => 'You\'re invited!',
        'footer' => 'Use your credit to fund a loan project here:'
    ],
    'sendwithusdefaults' => [
        'footer' => 'Don\’t miss our latest loan projects:',
        'button_text' =>'View Loans'
    ]
];
