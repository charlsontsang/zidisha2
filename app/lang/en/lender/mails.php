<?php
return [
    'new-loan-notification'              => [
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
    'loan-fully-funded'                  => [
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
    'lender-invite'                      => [
        'button-text' => 'Use Credit',
        'header'      => 'Zidisha invitation + $25 lending credit',
        'footer'      => 'Use your credit to fund a loan project here:',
        'subject'     => ':lenderName has sent you $25 to lend to a Zidisha entrepreneur',
        'body'        => '
:lenderName has sent you $25 to lend to a Zidisha entrepreneur.
<br/><br/>
:customMessage
Use your $25 to fund a small business growth loan to a disadvantaged entrepreneur in Africa or Asia.
You can communicate with your chosen entrepreneur via the Zidisha website as his or her business develops - changing a life while interacting directly with a remarkable individual on the other side of the world.'
    ],
    'lender-unused-fund'                 => [
        'subject' => 'You have unused funds in your Zidisha account',
        'body'    => 'Hi there,
                     <br/><br/>
                     Since you last visited Zidisha, repayments from entrepreneurs you\'ve supported have increased your lending account balance to :lenderBalance .
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
    'lender-account-abandoned'           => [
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
    'lender-invite-credit'               => [
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
    'sendwithus-defaults'                => [
        'footer'      => 'Don’t miss our latest loan projects:',
        'button-text' => 'View Loans'
    ],
    'loan-expired'                       => [
        'subject' => 'Your loan to :borrowerName has been returned',
        'body'    => 'Hi there,
We have a little bit of bad news.  It looks like :borrowerName ’s loan wasn’t fully funded.  We’ve returned your contribution of :bidAmount to your account.
<br><br>
But don\'t give up!  There are many more promising endeavors waiting to be funded.
<br><br>
Your lending credit balance is now :creditBalance. Use your credit to make a new loan <a href=":lendLink">here</a>.
<br><br>
Best wishes,
<br><br>
The Zidisha Team
<br><br>
PS:  You can sort the <a href=":lendLink">fundraising loans</a> by “Amount still needed” to find the ones that need the least money to be fully funded.'
    ],
    'loan-expired-invite'                => [
        'subject' => 'Your loan to :borrowerName has been returned',
        'body'    => '
Hi there,
We have a little bit of bad news.  It looks like :borrowerName ’s loan wasn’t fully funded.  We’ve returned your invite credit contribution of :bidAmount to your account.
<br><br>
But don\'t give up!  There are many more promising endeavors waiting to be funded.
<br><br>
Your lending credit balance is now :lenderInviteCreditBalance. Use your credit to make a new loan <a href=":lendLink">here</a>.
<br><br>
Best wishes,
<br><br>
The Zidisha Team
<br><br>
PS:  You can sort the <a href=":lendLink">fundraising loans</a> by “Amount still needed” to find the ones that need the least money to be fully funded.'
    ],
    'loan-disbursed'                     => [
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
    'loan-defaulted'                     => [
        'subject' => 'Write-off notification',
        'body'    => '
        Greetings,
<br/><br/>
We are writing to let you know that the amount remaining outstanding on <a href=":loanUrl">:borrowerName</a>\'s loan has been written off. To date, :borrowerName has repaid :repaidPercentage % of the :requestedAmount funded.
<br/><br/>
Under our reporting standards, outstanding loan amounts must be written off six months after a loan’s last scheduled repayment, or if no repayments are made for six months.
<br/><br/>
But we haven’t given up.  Many borrowers whose loans were written off have ultimately repaid.  If :borrowerName makes any further payments, we will credit them to your account.
<br/><br/>
Best wishes,
<br/><br/>
The Zidisha Team'
    ],
    'loan-repayment-received'            => [
        'subject'  => ':borrowerName sent you a repayment!',
        'body'     => '
Hi there,
<br/><br/>
You just received a repayment of :amount from <a href=":loanUrl">:borrowerName</a>!
<br/><br/>
This brings your lending credit balance up to :currentCredit.  You can re-lend these funds to new entrepreneurs <a href=":lendUrl">here</a>.
<br/><br/>
Too busy to select new loans manually? Activate automated relending of your repayments <a href=":autoLendingUrl">here</a>.
<br/><br/>
Keep spreading opportunities!  We’re thrilled and excited for more wonderful stories to unfold soon.
<br/><br/>
Cheers,
<br/><br/>
The Zidisha Team
<br/><br/>
PS:  You can adjust your account to send new lending credit emails only when your balance reaches a specified threshold <a href=:accountPreferenceUrl>here</a>;
',
        'message2' => '
        You received a :amount loan repayment from <a href=":loanUrl">:borrowerName</a>!<br/><br/>
Your lender credit balance is now :currentCredit. You may use this balance to make a new loan <a href=":lendUrl">here</a>.<br/><br/>
Best wishes,<br/><br/>
The Zidisha Team <br/><br/>
        '
    ],
    'loan-repayment-received-balance'    => [
        'subject' => 'Your lending credit has reached :currentCredit',
        'body'    => '
        Hi there,
<br/><br/>
Good news!  Thanks to repayments from entrepreneurs you have supported, your lending credit balance has reached :currentCredit.
<br/><br/>
You can re-lend these funds to new entrepreneurs <a href=":lendUrl">here</a>.  Spread the opportunity!  We’re thrilled and excited for wonderful stories to unfold soon.
<br/><br/>
Happy lending,
<br/><br/>
The Zidisha Team
        '
    ],
    'loan-repayment-feedback'            => [
        'subject' => 'Hooray! :borrowerName has fully repaid your loan',
        'header'  => ':borrowerName has fully repaid your loan.',
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
    'loan-repaid-gain'                   => [
        'subject' => ':gainAmount gain from your loan to :borrowerName',
        'body'    => '
        Hi there,
<br/><br/>
Your lending fund gained value from your recent loan to <a href=":loanUrl">:borrowerName</a>!  Here are the details:
<br/><br/>
Loan purpose: :purpose
<br/>
Amount lent: :loanAmount
<br/>
Amount repaid: :repaidAmount
<br/>
Net gain: :gainAmount
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
    'borrower-comment-notification'      => [
        'subject' => 'New message from :borrowerName',
        'body'    => ':message<br/><br/>
- :postedBy
:images',
    ],
    'lending-group-comment-notification' => [
        'subject' => 'New Message: :groupName Lending Group',
        'body'    => ':message<br/><br/>
- Posted by :byUserName on :date<br/><br/>
<a href=":groupLink">View and respond to the comment here.</a><br/><br/>
:images<br/><br/>
You may change your group comment notification preferences in the <a href=":groupLink"> :groupName Lending Group profile page</a>.'
    ],
    'out-bid-notification'               => [
        'subject' => 'Outbid Notification',
        'body'    => 'This is a notification that your bid to fund :bidAmount of the loan for <a href=\':borrowerLink\' target=\'_blank\'>:borrowerName</a> at :bidInterest % interest has been outbid by another lender who proposed a lower interest rate. The amount outbid of :outBidAmount has been returned to your lender account, and you may use it to fund another loan or to bid again on this one.<br/><br/>' .
            "Loan bids may be partially or fully outbid when the total value of lender bids exceeds the amount needed for the loan. In these cases, only the amount originally requested by the borrower is accepted, and bids at the lowest interest rates are retained. You may bid again on <a href=':borrowerLink' target='_blank'>:borrowerName</a>'s loan by proposing a lower interest rate.<br/><br/>" .
            "Best wishes,<br/><br/>" .
            'The Zidisha Team'
    ],
    'down-bid-notification'              => [
        'subject' => 'Outbid Notification',
        'body'    => 'This is a notification that :outBidAmount of your bid to fund :bidAmount of the loan for <a href=\':borrowerLink\' target=\'_blank\'>:borrowerName</a> at :bidInterest % interest has been outbid by another lender who proposed a lower interest rate. The remaining value of your bid for this loan is :remainedBidAmount. The amount outbid of :outBidAmount has been returned to your lender account, and you may use it to fund another loan or to bid again on this one.<br/><br/>' .
            "Loan bids may be partially or fully outbid when the total value of lender bids exceeds the amount needed for the loan. In these cases, only the amount originally requested by the borrower is accepted, and bids at the lowest interest rates are retained. You may bid again on <a href=':borrowerLink' target='_blank'>:borrowerName</a>'s loan by proposing a lower interest rate.<br/><br/>" .
            "Best wishes,<br/><br/>" .
            "The Zidisha Team"
    ],
    'register-welcome'                   => [
        'subject' => 'Welcome to Zidisha!',
        'body'    => "Hi there!
<br/><br/>
Welcome to the Zidisha community! I’m Julia, the director of Zidisha - and I am so thrilled and honored that you’ve joined us.
<br/><br/>
Zidisha is different from other microlending websites.  We bypass local banks, so that the entrepreneurs don’t have to pay high administrative charges for your loans.  And since our entrepreneurs are web users too, you can communicate with them directly through their loan profile pages.  Give it a try!
<br/><br/>
To get started, go to the <a href=':lendLink'>Lend</a> page and start exploring!  You can browse by country and business type until you find an entrepreneur you’d like to connect with.
<br/><br/>
Zidisha is staffed by an amazing team of volunteers who are always available to help you.  You can reach us at Facebook, Twitter or by email to service@zidisha.org.
<br/><br/>
Can’t wait to change the world with you,
<br/><br/>
Julia Kurnia
<br/><br/>
PS:  Head over to our <a href='http://p2p-microlending-blog.zidisha.org/'>blog</a> for inspiring stories from our most remarkable entrepreneurs!"
    ],
    'loan-about-to-expire'               => [
        'subject' => ':borrowerName has only 3 days left',
        'body'    => "Hi there,
<br/><br/>
Just a heads up: :borrowerName’s loan application, which you supported on :recentBidDate, is about to expire.
<br/><br/>
:borrowerName still needs to raise :amountStillNeeded in the next three days! We know you want to see this loan get fully funded — so here’s how you can help:
<br/>
<ul>
	<li>
		<a href=':loanLink'>LEND</a> more and help :borrowerName complete the loan with your generous addition.
	</li>
	<li>
		<a href=':inviteLink'>SEND a free $25 credit</a> to a friend who can use it to help fund :borrowerName’s loan.
	</li>
	<li>
		SHARE :borrowerName's loan request link via Facebook and Twitter to inspire others to contribute, too: <a href=':loanLink'>:loanLink</a>
	</li>
</ul>
<br/>
If :borrowerName's loan expires without being fully funded, the application will close and the funds you’ve contributed will be returned to your lending account.
<br/><br/>
Warmly,
<br/><br/>
The Zidisha Team"
    ],
    'allow-loan-forgiveness'             => [
        'subject' => 'Your loan to :borrowerName',
        'body'    => 'We are writing to inform you of difficulties experienced by :borrowerName, whose loan you funded on :disbursedDate. You may choose to forgive :borrowerName\'s loan using the button below.<br/><br/>
:message<br/><br/>
In exceptional cases, Zidisha offers lenders the option to forgive loans to borrowers who have experienced an unexpected misfortune which affects their ability to repay the loan. In these cases, each lender has the option to forgive his or her share of the loan. Such decisions will remain anonymous and are completely at each lender\'s discretion.<br/><br/>
Should you decide to forgive this loan, you will be declining to receive further repayments from :borrowerName, and the loan\'s outstanding balance of :outstandingAmount will be reduced by the amount that had been remaining due to you under the original loan agreement.<br/><br/>
Would you like to forgive your share of this loan?<br/><br/>
<a class=\'btn\' href=\':yesLink%\' target=\'_blank\'><img alt=\'Forgive.\' src=\':yesImage\' ></a>
&nbsp;&nbsp;&nbsp;&nbsp;
<br/><a class=\'btn\' href=\':noLink\' target=\'_blank\'><img alt=\'Do Not Forgive\' src=\':noImage\'></a><br/>
<a href=\':loanLink\' target=\'_blank\'>View Loan Profile</a><br/><br/>
Best wishes,<br/><br/>
The Zidisha Team'
    ],
    'abandoned-user-mail'                => [
        'subject' => 'Zidisha account expiration notification',
        'body'    => 'Dear :lenderName,<br/><br/>
We noticed that you have not logged into your account at Zidisha.org for over one year.  We\'re sorry that lending with Zidisha did not work out for you, and would sincerely welcome any feedback you would care to share regarding why you have not come back.<br/><br/>
Should you desire to maintain access to your lender credit balance, simply log in to your member account at <a href=":siteLink">Zidisha.org</a> at any time within the next month. If you do not wish to keep your account open, you need not do anything: we will close your account and convert any remaining lender credit to a donation on :expireDate.<br/><br/>
Thanks so much for having participated in our lending community, and for helping to extend life-changing opportunities to some of the world\'s most marginalized entrepreneurs.<br/><br/>
Best regards,<br/><br/>
The Zidisha Team
        '
    ],
    'paypal-withdraw'                    => [
        'subject' => 'Message from Zidisha',
        'body'    => 'Hello :lenderName,
<br/><br/>You have successfully withdrawn :withdrawnAmount from your Zidisha account. The funds have been deposited in your PayPal account.
<br/><br/>Thank you for your generous support and partnership with us,
<br/><br/>
Zidisha Team'
    ],
    'fund-upload'                        => [
        'subject' => "You have new lending credit!",
        'body'    => "
Hi there,
<br/><br/>
Thank you for your lender funds upload of :uploadAmount !  We have just credited your payment to your lending account - which means it’s time to start browsing new borrower projects!
<br/><br/>
Use your credit to make a new loan <a href=':lendUrl'>here</a>.  We can't wait to see which life-changing projects you make happen next!
<br/><br/>
Happy lending,
<br/><br/>
The Zidisha Team",
    ],
    'lender-donation'                    => [
        'subject' => "Zidisha Donation Receipt",
        'body'    => "
Zidisha Inc.
<br/>
46835 Muirfield Court #301
<br/>
Sterling, Virginia 20164
<br/><br/>
Hi, I’m Julia, the Director of Zidisha, and I'm thrilled and humbled that you donated to our nonprofit today.
<br/><br/>
This message may be used as a receipt for your donation of :donationAmount on :donationDate.  Zidisha Inc. is a 501(c)(3) charitable organization per the United States Internal Revenue Service, and did not provide any goods or services in exchange for your donation. Our Employment Identification Number (EIN) is 80-049-4876.
<br/><br/>
Warmly,
<br/><br/>
Julia Kurnia
<br/>
Director, Zidisha Inc.
",
    ],
    'loan-forgiveness-confirmation'      => [
        'subject' => "Forgiveness confirmation",
        'body'    => "Thank you for forgiving your share in remaining repayments by <a href=':borrowerUrl' target='_blank'>:borrowerName</a>. The remaining amount owed by :borrowerName has been reduced by :reducedAmount.<br/><br/>" .
            "Best wishes,<br/><br/>" .
            "The Zidisha Team",
    ],
    'password-reset'                     => [
        'subject' => 'Password Reset',
        'body'    => "To reset your password, complete this form: :formLink .<br/>
			This link will expire in :expireTime minutes.",
    ],
    'invitee-own-funds'                  => [
        'subject' => 'Make a new loan happen',
        'body'    => "Hi there,
<br/><br/>
Since :inviterUsername invited you to join Zidisha, you've helped make dreams come true for at least one deserving entrepreneur.  Congrats!
<br/><br/>
Ready for round two? You can actually start growing your own loan portfolio for as little as ONE DOLLAR. (Yes, it’s that simple!) And to make things even easier, we’ve picked out a few promising projects you can back right now:",
        'footer'  => 'View more loan opportunities here:',
    ],
];
