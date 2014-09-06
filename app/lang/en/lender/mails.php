<?php
return [
    'new-loan-notification'           => [
        'subject'       => ':borrowerName has posted a new loan application!',
        'lender-body'   => ':borrowerName fully repaid the loan you funded on :repayDate, and has just posted a new loan application!
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
    'loan-fully-funded'               => [
        'subject'          => ':borrowerName is fully funded!',
        'body'             => '',
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
    'lender-invite'                   => [
        'subject' => ':lenderName has sent you $25 to lend to a Zidisha entrepreneur',
        'body'    => '
:lenderName has sent you $25 to lend to a Zidisha entrepreneur.
<br/>
:customMessage
Use your $25 to fund a small business growth loan to a disadvantaged entrepreneur in Africa or Asia.
You can communicate with your chosen entrepreneur via the Zidisha website as his or her business develops - changing a life while interacting directly with a remarkable individual on the other side of the world.'
    ],
    'lender-unused-fund'              => [
        'subject' => 'You have unused funds in your Zidisha account',
        'body'    => 'Hi there,
                     <br/><br/>
                     Since you last visited Zidisha, repayments from entrepreneurs you\'ve supported have increased your lending account balance to USD :lenderBalance.
                     <br/><br/>
                     We know you’re busy, and wanted to help you out by picking out three amazing loan projects you can support right now:',
        'extra'   => 'Happy lending,
                            <br/><br/>
                            The Zidisha Team
                            <br/><br/>
                            PS:  Use our automated lending tool to continuously reinvest your repayments in new loans!  Set it up <a href=":automaticLendingLink">here</a>.',
        'header'  => 'You\'re invited!',
        'footer'  => 'View more fundraising loan projects here!'
    ],
    'lender-account-abandoned'        => [
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
    'lender-invite-credit'            => [
        'subject'     => 'Your invite is accepted!',
        'body'        => '
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
        'footer'      => 'Redeem your credit by making a loan here:',
        'button-text' => 'Make A Loan'
    ],
    'sendwithus-defaults'             => [
        'footer'      => 'Don\’t miss our latest loan projects:',
        'button-text' => 'View Loans'
    ],
    'loan-expired'                    => [
        'subject' => 'Your loan to :borrowerName has been returned',
        'body'    => 'Hi there,
We have a little bit of bad news.  It looks like :borrowerName ’s loan wasn’t fully funded.  We’ve returned your contribution of :bidAmount to your account.
<br><br>
But don\'t give up!  There are many more promising endeavors waiting to be funded.
<br><br>
Your lending credit balance is now USD :creditBalance. Use your credit to make a new loan <a href=":lendLink">here</a>.
<br><br>
Best wishes,
<br><br>
The Zidisha Team
<br><br>
PS:  You can sort the <a href=":lendLink">fundraising loans</a> by “Amount still needed” to find the ones that need the least money to be fully funded.'
    ],
    'loan-expired-invite'             => [
        'subject' => 'Your loan to :borrowerName has been returned',
        'body'    => '
Hi there,
We have a little bit of bad news.  It looks like :borrowerName ’s loan wasn’t fully funded.  We’ve returned your invite credit contribution of :bidAmount to your account.
But don\'t give up!  There are many more promising endeavors waiting to be funded.
Your lending credit balance is now USD :lenderInviteCreditBalance. Use your credit to make a new loan <a href=":lendLink">here</a>.

Best wishes,
The Zidisha Team

PS:  You can sort the <a href=":lendLink">fundraising loans</a> by “Amount still needed” to find the ones that need the least money to be fully funded.'
    ],
    'loan-disbursed'                  => [
        'subject' => ':borrowerName has received your loan!',
        'message' => ':borrowFirstName has received your loan.',
        'body'    => 'Good news!  We transferred your loan to :borrowerName on :disbursedDate. :borrowFirstName is now on the way to achieving a brighter future, thanks to you.
<br/><br/>
Your loan disbursement is just the beginning. Keep informed of progress and interact with :borrowFirstName via the <a href=":loanPage">loan profile page</a>. Don\'t be shy! We encourage you to post comments and questions for :borrowFirstName throughout the lending period.<br/><br/>
Cheers,<br/><br/>
The Zidisha Team
<br/><br/>
PS:  Want a really cool gift idea?  Check out our <a href=":giftCardPage">gift cards</a> - your friend chooses the entrepreneur!'
    ],
    'loan-defaulted'                  => [
        'subject' => 'Write-off notification',
        'body'    => '
        Greetings,
<br/><br/>
We are writing to let you know that the amount remaining outstanding on <a href=":loanUrl">:borrowerName</a>\'s loan has been written off. To date, :borrowerName has repaid :repaidPercentage % of the USD :requestedAmount funded.
<br/><br/>
Under our reporting standards, outstanding loan amounts must be written off six months after a loan’s last scheduled repayment, or if no repayments are made for six months.
<br/><br/>
But we haven’t given up.  Many borrowers whose loans were written off have ultimately repaid.  If :borrowerName makes any further payments, we will credit them to your account.
<br/><br/>
Best wishes,
<br/><br/>
The Zidisha Team'
    ],
    'loan-repayment-received'         => [
        'subject'  => ':borrowerName sent you a repayment!',
        'body'     => '
Hi there,
<br/><br/>
You just received a repayment of USD :amount from <a href=":loanUrl">:borrowerName</a>!
<br/><br/>
This brings your lending credit balance up to USD :currentCredit.  You can re-lend these funds to new entrepreneurs <a href=":lendUrl">here</a>.
<br/><br/>
Too busy to select new loans manually? Activate automated relending of your repayments <a href=":autoLendingUrl">here</a>.
<br/><br/>
You can adjust your email notification preferences <a href=:accountPreferenceUrl>here</a>.
<br/><br/>
Keep spreading opportunities!  We’re thrilled and excited for more wonderful stories to unfold soon.
<br/><br/>
Cheers,
<br/><br/>
The Zidisha Team',
        'message2' => '
        You received a USD :amount loan repayment from <a href=":loanUrl">:borrowerName</a>!<br/><br/>
Your lender credit balance is now USD :currentCredit. You may use this balance to make a new loan <a href=":lendUrl">here</a>.<br/><br/>
Best wishes,<br/><br/>
The Zidisha Team <br/><br/>
        '
    ],
    'loan-repayment-received-balance' => [
        'subject' => 'Your lending credit has reached USD :currentCredit',
        'body'    => '
        Hi there,
<br/><br/>
Good news!  Thanks to repayments from entrepreneurs you have supported, your lending credit balance has reached USD :currentCredit.
<br/><br/>
You can re-lend these funds to new entrepreneurs <a href=":lendUrl">here</a>.  Spread the opportunity!  We’re thrilled and excited for wonderful stories to unfold soon.
<br/><br/>
Happy lending,
<br/><br/>
The Zidisha Team
        '
    ],
    'loan-repayment-feedback'         => [
        'subject' => 'Hooray! :borrowerName has completely repaid your loan',
        'header'  => ':borrowerName has completely repaid your loan.',
        'body'    => '
        Hi there,
<br/><br/>
Great news: :borrowerName has completely repaid your loan!
<br/><br/>
Now you can help :borrowerName create a performance record to help in raising future loans.  Post a review of your lending experience <a href=":reviewUrl">here</a>.
<br/><br/>
Cheers,
<br/><br/>
The Zidisha Team
<br/><br/>
PS:  Share, tweet or email this success story to your friends!'
    ],
    'loan-repaid-gain'                => [
        'subject' => ':gainAmount gain from your loan to :borrowerName',
        'body'    => '
        Hi there,
<br/><br/>
Your lending fund gained value from your recent loan to <a href=":loanUrl">:borrowerName</a>!  Here are the details:
<br/><br/>
Loan purpose: :purpose
<br/>
Amount lent: $ :loanAmount
<br/>
Amount repaid: $ :repaidAmount
<br/>
Net gain: $ :gainAmount
<br/>
Percentage gain: :gainPercent %
<br/><br/>
You can make this a win for everyone by reinvesting the gain in new loans to other entrepreneurs <a href=":lendUrl">here</a>.
<br/><br/>
Here\'s to many more successful loans!
<br/><br/>
Congrats,
<br/><br/>
The Zidisha Team
<br/><br/>
PS:  You can view the gains or losses for all of your completed loans <a href=":myStatsUrl">here</a>.
        '
    ],
    'borrower-comment-notification'   => [
        'subject' => 'New message from :borrowerName',
        'body'    => ':message<br/><br/>
- :postedBy
:images',
    ],
];
