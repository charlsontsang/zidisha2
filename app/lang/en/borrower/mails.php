<?php

return [
    'loan-confirmation'            => [
        'subject' => 'Your Loan Application Has Been Published',
        'body'    => 'Dear :borrowerName,<br/><br/>
Congratulations!  Your loan application has been posted for funding.  Click <a href=":loanApplicationPage">here</a> to view your loan application page.<br/><br/>
Please note that your application will be posted for a maximum of :loanApplicationDeadLine days, or until it is fully funded and you choose to accept the bids raised. You may edit your loan application page at any time using the <a href=":loanApplicationLink">Loan Application</a> page.<br/><br/>
Best of luck in your endeavor,<br/><br/>
The Zidisha Team'
    ],
    'loan-fully-funded'            => [
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
    'loan-disbursed'               => [
        'subject' => 'Loan disbursement confirmation',
        'body'    => 'Dear :borrowerName ,<br/><br/>This is to confirm disbursement of your Zidisha loan in the amount of :disbursedAmount. If this is your first Zidisha loan, the new client registration fee of :registrationFee was deducted from your loan disbursement for a net payment of :netAmount.<br/><br/>".
"To view your repayment schedule please log into your account at <a href=":zidishaLink" target=\'_blank\'>www.zidisha.org</a> and click on "Repayment Schedule"<br/><br/>' .
            ':repaymentInstruction<br/><br/>' .
            'Now, we\'d like to ask for your help. Please log in to your account at <a href=":zidishaLink" target=\'_blank\'>www.zidisha.org</a> and click "Post a Comment". Then type a comment to let lenders know exactly what you have been able to purchase with the loan, and how it has helped you.  Regular communication with lenders regarding the results of their loan will help establish good relations such that that they will be happy to lend to you again in the future.<br/><br/>' .
            'Should you have any questions, please do not hesitate to contact us at service@zidisha.org.<br/><br/>' .
            'We wish you much success in your endeavor.<br/><br/>' .
            'Zidisha Team'
    ],
    'loan-arrear-reminder-final'   => [
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
    'loan-arrear-reminder-first'   => [
        'subject' => '',
        'body'    => 'Dear :borrowerName,<br/><br/>
This is notification that we did not receive your loan repayment installment of :dueAmt, which was due on :dueDate.<br/><br/>
Please make the past due payment immediately following the instructions below.<br/><br/>
:repaymentInstructions<br/><br/>
Thank you,<br/><br/>
The Zidisha Team<br/><br/>',
    ],
    'loan-arrear-reminder-monthly' => [
        'subject' => 'Past Due Loan Mediation Requested',
        'body'    => 'Dear :borrowerName,

This is notification that, in accordance with the terms of the Loan Contract, we have requested mediation from one or more of the following individuals regarding your past due loan balance.
:contacts
<br>
Please send make this payment immediately following the bank deposit instructions in your Zidisha.org member account. If you are unable to make the past due payment immediately, you may use the \'Reschedule Loan\' page of your member account at Zidisha.org to propose an alternative repayment schedule to lenders.

If you do not reschedule and we do not receive the past due amount, then we will continue to contact and request mediation from members of your community. Thank you, Zidisha Team'
    ],
    'reminder-advance'             => [
        'subject' => 'Reminder from Zidisha',
        'body'    => 'Dear :borrowerName,<br/><br/>
This is a courtesy reminder that your next loan repayment installment will be due on :dueDate.<br/><br/>
You currently have an advance payment credit of :paidAmt. This will be credited toward your balance due on :dueDate, for a net amount of :dueAmt due on :dueDate.<br/><br/>
:repaymentInstructions<br/><br/>
Please ensure that the due payment is made promptly, and contact us in case of difficulty.<br/><br/>
Thank you,<br/><br/>
The Zidisha Team'
    ],
    'reminder'                     => [
        'subject' => 'Reminder from Zidisha',
        'body'    => 'Dear :borrowerName,<br/><br/>
This is a courtesy reminder that your next loan repayment installment will be due on :dueDate in the amount of :dueAmt.<br/>
:repaymentInstructions<br/><br/>
Please ensure that the due payment is made promptly, and contact us in case of difficulty.<br/><br/>
Thank you,<br/><br/>
The Zidisha Team'
    ],
    'reminder-postDue'             => [
        'subject' => 'Reminder from Zidisha',
        'body'    => 'Dear :borrowerName,<br/><br/>
This is a courtesy reminder that your next loan repayment installment will be due on :dueDate.<br/><br/>
You currently have a past due balance of :pastDueAmt. This will be added to your balance due on :dueDate, for a total amount of :dueAmt due on :dueDate.<br/>
:repaymentInstructions<br/><br/>
Please ensure that the due payment is made promptly, and contact us in case of difficulty.<br/><br/>
Thank you,<br/><br/>
The Zidisha Team'
    ],
    'reminder-again'               => [
        'subject' => 'Reminder from Zidisha',
        'body'    => 'Dear :borrowerName,<br/><br/>
This is a notification of your Zidisha loan repayment balance of :dueAmt, which has been past due since :dueDate, has not been received to date.<br/><br/>
:repaymentInstructions<br/><br/>
Please deposit the past due amount immediately.  If you are experiencing difficulty or believe you have received this message in error, please contact us at service@zidisha.org.<br/><br/>
Thank you,<br/><br/>
Zidisha Team'
    ],
    'email-verification'           => [
        'subject' => 'Please confirm your email address',
        'body'    => 'Thank you for creating a Zidisha account! To confirm your email address, simply click the confirmation link at the bottom of this email.<br/><br/>' .
            '<a href=":verifyLink">Verify</a><br/>
You may also paste the link into the address bar of your internet browser and press Enter or Return to complete the confirmation.'
    ],
    'registration-join'            => [
        'subject' => 'Zidisha application submitted',
        'body'    => 'Dear :borrowerName, <br/><br/>Thank you for your application to join Zidisha.<br/><br/>A Zidisha staff member will now review your account, a process that normally takes up to one week.  You will be notified by email when the review is complete.  You may also log in to Zidisha to check the status of your account at any time.<br/><br/>

Regards,<br/><br/>

The Zidisha Team'
    ],
    'resume-registration'          => [
        'subject' => 'Continue Borrower Registration.',
        'body'    => 'Please click on this link to continue working on your application:
<br/>
<a href=":resumeLink">Resume Application</a>
<br/>
Your application code is given below for your reference.
<br/>
:resumeCode'
    ],

];
