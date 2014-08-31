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
        'subject' => ':borrowerName is fully funded!',
        'body'    => '',
        'accept-message-1' => 'New opportunities are opening up for :borrowerName',
        'accept-message-2' => '
Hooray!  You made it happen: <a href=":borrowerProfileLink">:borrowerName</a>\'s loan is now fully funded.
<br/><br/>
Our volunteer team is transferring your loan to :borrowerName now.  In the meantime, feel free to shoot questions, comments and kudos to us at <a href="http://www.facebook.com/ZidishaInc">Facebook</a>, <a href="https://twitter.com/ZidishaInc">Twitter</a> or by email to service@zidisha.org.
<br/><br/>
Love,
<br/><br/>
The Zidisha Team
<br/><br/>
PS:  Join a <a href=":lendingGroupLink">Lending Group</a> to meet other lenders who share your interests!
'
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
        'footer' => 'View more fundraising loan projects here!'
    ],
    'lender-account-abandoned' => [
        'body' => 'Dear :lenderName,
<br/><br/>
We noticed that you have not logged into your account at Zidisha.org for over one year.  
We\'re sorry that lending with Zidisha did not work out for you, and would sincerely welcome any feedback you would care to share regarding why you have not come back.
<br/><br/>
Should you desire to maintain access to your lender credit balance, simply log in to your member account at <a href=":siteLink">Zidisha.org</a> at any time within the next month. 
If you do not wish to keep your account open, you need not do anything: we will close your account and convert any remaining lender credit to a donation on :expiryDate.
<br/><br/>
Thanks so much for having participated in our lending community, and for helping to extend life-changing opportunities to some of the world\'s most marginalized entrepreneurs.
<br/><br/>
Best regards,<br/><br/>
The Zidisha Team'
    ],
    'lender-invite-credit' => [
        'subject' => 'Your invite is accepted!',
        'body' => '
Greetings,
<br/><br/>
You’ve done something wonderful.  The invite you sent to :inviteeMail has just been accepted. Zidisha has a new member thanks to you!
<br/><br/>
To express our appreciation, we\'ve added to your account a matching USD 25 lending credit.
You may use this credit to fund a loan of your choice <a href=":lendingPage">here</a>.
<br/><br/>
Don\'t forget to welcome :inviteeMail to Zidisha - and watch your combined impact grow with each new opportunity that opens up because you made this connection.
<br/><br/>
Best wishes,
<br/><br/>
The Zidisha Team',
        'footer' => 'Redeem your credit by making a loan here:',
        'button-text' => 'Make A Loan'
    ],
    'sendwithusdefaults' => [
        'footer' => 'Don\’t miss our latest loan projects:',
        'button_text' =>'View Loans'
    ]
];
