<?php

return [
    'loan-confirmation'                  => [
        'subject' => 'Your Loan Application Has Been Published',
        'body'    => 'Dear :borrowerName,<br/><br/>
Congratulations!  Your loan application has been posted for funding.  Click <a href=":loanApplicationPage">here</a> to view your loan application page.<br/><br/>
Please note that your application will be posted for a maximum of :loanApplicationDeadLine days, or until it is fully funded and you choose to accept the bids raised. You may edit your loan application page at any time using the <a href=":loanApplicationLink">Loan Application</a> page.<br/><br/>
Best of luck in your endeavor,<br/><br/>
The Zidisha Team'
    ],
    'loan-fully-funded'                  => [
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
    'loan-disbursed'                     => [
        'subject' => 'Loan disbursement confirmation',
        'body'    => 'Dear :borrowerName ,<br/><br/>This is to confirm disbursement of your Zidisha loan in the amount of :disbursedAmount. If this is your first Zidisha loan, the new client registration fee of :registrationFee was deducted from your loan disbursement for a net payment of :netAmount.<br/><br/>".
"To view your repayment schedule please log into your account at <a href=":zidishaLink" target=\'_blank\'>www.zidisha.org</a> and click on "Repayment Schedule"<br/><br/>' .
            ':repaymentInstruction<br/><br/>' .
            'Now, we\'d like to ask for your help. Please log in to your account at <a href=":zidishaLink" target=\'_blank\'>www.zidisha.org</a> and click "Post a Comment". Then type a comment to let lenders know exactly what you have been able to purchase with the loan, and how it has helped you.  Regular communication with lenders regarding the results of their loan will help establish good relations such that that they will be happy to lend to you again in the future.<br/><br/>' .
            'Should you have any questions, please do not hesitate to contact us at service@zidisha.org.<br/><br/>' .
            'We wish you much success in your endeavor.<br/><br/>' .
            'Zidisha Team'
    ],
    'loan-arrear-reminder-final'         => [
        'subject' => 'Past Due Loan Final Notice',
        'body'    => 'Dear :borrowerName,<br/><br/>
This is a final notice that we did not receive your loan repayment installment of :dueAmt, which was due on :dueDate.<br/><br/>
Please make the past due payment immediately following the instructions below. If you are unable to make the past due payment immediately, you may use the \'Reschedule Loan\' page of your member account at Zidisha.org to propose an alternative repayment schedule to lenders.<br/><br/>
If you do not reschedule and we do not receive the past due amount, then we will contact and request mediation from members of your community, including but not limited to the individuals whose contacts you provided in support of your loan application:<br/>
:contacts<br/><br/>
:repaymentInstructions<br/><br/>

Thank you,<br/><br/>
The Zidisha Team'
    ],
    'loan-arrear-reminder-first'         => [
        'subject' => 'Past Due Loan Notification',
        'body'    => 'Dear :borrowerName,<br/><br/>
This is notification that we did not receive your loan repayment installment of :dueAmt, which was due on :dueDate.<br/><br/>
Please make the past due payment immediately following the instructions below.<br/><br/>
:repaymentInstructions<br/><br/>
Thank you,<br/><br/>
The Zidisha Team<br/><br/>',
    ],
    'loan-arrear-reminder-monthly'       => [
        'subject' => 'Past Due Loan Mediation Requested',
        'body'    => 'Dear :borrowerName,

This is notification that, in accordance with the terms of the Loan Contract, we have requested mediation from one or more of the following individuals regarding your past due loan balance.
:contacts
<br>
Please send make this payment immediately following the bank deposit instructions in your Zidisha.org member account. If you are unable to make the past due payment immediately, you may use the \'Reschedule Loan\' page of your member account at Zidisha.org to propose an alternative repayment schedule to lenders.

If you do not reschedule and we do not receive the past due amount, then we will continue to contact and request mediation from members of your community. Thank you, Zidisha Team'
    ],
    'reminder-advance'                   => [
        'subject' => 'Reminder from Zidisha',
        'body'    => 'Dear :borrowerName,<br/><br/>
This is a courtesy reminder that your next loan repayment installment will be due on :dueDate.<br/><br/>
You currently have an advance payment credit of :paidAmt. This will be credited toward your balance due on :dueDate, for a net amount of :dueAmt due on :dueDate.<br/><br/>
:repaymentInstructions<br/><br/>
Please ensure that the due payment is made promptly, and contact us in case of difficulty.<br/><br/>
Thank you,<br/><br/>
The Zidisha Team'
    ],
    'reminder'                           => [
        'subject' => 'Reminder from Zidisha',
        'body'    => 'Dear :borrowerName,<br/><br/>
This is a courtesy reminder that your next loan repayment installment will be due on :dueDate in the amount of :dueAmt.<br/>
:repaymentInstructions<br/><br/>
Please ensure that the due payment is made promptly, and contact us in case of difficulty.<br/><br/>
Thank you,<br/><br/>
The Zidisha Team'
    ],
    'reminder-postDue'                   => [
        'subject' => 'Reminder from Zidisha',
        'body'    => 'Dear :borrowerName,<br/><br/>
This is a courtesy reminder that your next loan repayment installment will be due on :dueDate.<br/><br/>
You currently have a past due balance of :pastDueAmt. This will be added to your balance due on :dueDate, for a total amount of :dueAmt due on :dueDate.<br/>
:repaymentInstructions<br/><br/>
Please ensure that the due payment is made promptly, and contact us in case of difficulty.<br/><br/>
Thank you,<br/><br/>
The Zidisha Team'
    ],
    'reminder-again'                     => [
        'subject' => 'Reminder from Zidisha',
        'body'    => 'Dear :borrowerName,<br/><br/>
This is a notification of your Zidisha loan repayment balance of :dueAmt, which has been past due since :dueDate, has not been received to date.<br/><br/>
:repaymentInstructions<br/><br/>
Please deposit the past due amount immediately.  If you are experiencing difficulty or believe you have received this message in error, please contact us at service@zidisha.org.<br/><br/>
Thank you,<br/><br/>
Zidisha Team'
    ],
    'email-verification'                 => [
        'subject' => 'Please confirm your email address',
        'body'    => 'Thank you for creating a Zidisha account! To confirm your email address, simply click the confirmation link at the bottom of this email.<br/><br/>' .
            '<a href=":verifyLink">Verify</a><br/>
You may also paste the link into the address bar of your internet browser and press Enter or Return to complete the confirmation.'
    ],
    'registration-join'                  => [
        'subject' => 'Zidisha application submitted',
        'body'    => 'Dear :borrowerName, <br/><br/>Thank you for your application to join Zidisha.<br/><br/>A Zidisha staff member will now review your account, a process that normally takes up to one week.  You will be notified by email when the review is complete.  You may also log in to Zidisha to check the status of your account at any time.<br/><br/>

Regards,<br/><br/>

The Zidisha Team'
    ],
    'resume-registration'                => [
        'subject' => 'Continue Borrower Registration.',
        'body'    => 'Please click on this link to continue working on your application:
<br/>
<a href=":resumeLink">Resume Application</a>
<br/>
Your application code is given below for your reference.
<br/>
:resumeCode'
    ],
    'volunteer-mentor-confirmation'      => [
        'subject' => 'New assigned member: :borrowerName',
        'body'    => 'Dear :vmName,<br/><br/>
:borrowerName has applied to join Zidisha and has selected you as a Volunteer Mentor. We encourage you to review :borrowerName\'s profile here: <a href=":profileUrl">View</a><br/><br/>
If you have any concerns about the information :borrowerName has provided, please let us know by replying to this email.<br/><br/>
Thank you,<br/><br/>
The Zidisha Team'
    ],
    'approved-confirmation'              => [
        'subject' => 'Zidisha Account Activation',
        'body'    => 'Dear :borrowerName, <br/><br/>
Congratulations! Your application to join Zidisha has been approved.<br/><br/>
Zidisha is an internet-based community based on earned trust.  Membership is highly selective, and being accepted into Zidisha is something to take pride in.<br/><br/>
You are now eligible to offer a loan agreement to Zidisha lenders.  To post a loan application on Zidisha, please log in to your member account at <a href=":zidishaLink" target="_blank">Zidisha.org</a> and follow the instructions.<br/><br/>
Best wishes,<br/><br/>
The Zidisha Team'
    ],
    'declined-confirmation'              => [
        'subject' => 'Message from Zidisha',
        'body'    => 'Dear :borrowerName, <br/><br/>' .
            'We regret to inform you that your Zidisha account cannot be activated, because we were unable to confirm that your account meets all required criteria for a Zidisha loan.<br/><br/>
Best wishes,<br/><br/>' .
            'Zidisha Team'
    ],
    'loan-expired'                       => [
        'subject' => 'Your Loan Application Has Expired',
        'body'    => 'Dear :borrowerName,<br/><br/>
This is a notification that your Zidisha loan application has expired without being fully funded, and the loan bids raised have been returned to lenders.<br/><br/>
You may post a new loan application at any time using the <a href=":loanApplicationLink">Loan Application</a> page of your member account.<br/><br/>
:tips
<br/><br/>
If you modify your profile to follow each of the above tips, then your application is much more likely to be funded the next time.  Should you have any questions or difficulties, please do not hesitate to contact us at service@zidisha.org.<br/><br/>
Best wishes,<br/><br/>
The Zidisha Team'
    ],
    'invite'                             => [
        'link' => 'Go to <a href=":borrowLink">Join and Accept Invitation</a> to accept this invite or to learn more.'
    ],
    'loan-arrear-mediation-notification' => [
        'subject' => 'Mediation request from Zidisha',
        'body'    => 'Dear :contactName,<br/><br/>
You had invited or endorsed :borrowerName\'s application to join our organization, Zidisha Microfinance. :borrowerName is now :dueDays days in arrears on the loan taken from Zidisha.<br/><br/>
Can you please contact :borrowerName at :borrowerNumber and help us find out why we have not received the past due loan repayments?<br/><br/>
You may contact us by replying to this email. Thanks very much for your help.<br/><br/>
The Zidisha Team'
    ],
    'borrower-comment-notification'      => [
        'footer'      => 'View and respond to the comment here:',
        'button-text' => 'View Comment',
        'subject'     => 'You Received a Message at Zidisha',
        'body'        => 'Dear :borrowerName,<br/><br/>
You have a new message on your Zidisha loan page.<br/><br/>
:postedBy<br/><br/>
:message<br/><br/> :images <br/> <br/>' .
            'Please log in to your account at www.zidisha.org and click "Post a comment" to respond to this message.<br/><br/>' .
            'Thank you,<br/><br/>' .
            'Zidisha Team',
    ],
    'payment-receipt'                    => [
        'subject' => 'Zidisha Payment Received',
        'body'    => "Dear :borrowerName,<br/><br/>
Thank you for your loan repayment of :repaidAmount. This amount has been credited to your Zidisha account balance.<br/><br/>
Now, we'd like to ask for your help. Please log in to your Zidisha website account and click \"Post a Comment\" in the Welcome page. Then type a comment to let lenders know what new things have happened in your business, family or your own life, especially if something has improved thanks to the loan.  Add photos if possible!  Regular communication with lenders will help establish good relations such that that they will be happy to lend to you again in the future.<br/><br/>
Best wishes,<br/><br/>
The Zidisha Team"
    ],
];
