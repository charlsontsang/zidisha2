<?php

return [
    'contact-confirmation'               =>
        'Dear :contactName, :borrowerName of tel. :borrowerPhoneNumber has shared
            your contacts in an application to join the Zidisha.org online lending community.

            We would like to confirm with you that :borrowerName
            can be trusted to repay loans. If you do not know or do not recommend
            :borrowerName, please inform us by SMS reply to this number. Thank you.',
    'final-arrear-notification'          =>
        'Dear :borrowerName, this is a final notice of your outstanding loan repayment of :currencyCode :dueAmt, which was due on :dueDate.

Please send make this payment immediately following the payment instructions in your Zidisha.org member account. If you are unable to make the past due payment immediately, you may use the \'Reschedule Loan\' page of your member account at Zidisha.org to propose an alternative repayment schedule to lenders.

If you do not reschedule and we do not receive the past due amount, then we will contact and request mediation from members of your community, including but not limited to the individuals whose contacts you provided in support of your loan application:
:contacts
<br>
Thank you, Zidisha Team',
    'first-arrear-notification'          =>
        'Dear :borrowerName, we did not receive your loan repayment of :currencyCode :dueAmt, which was due on :dueDate. Please make this payment immediately. Thank you, Zidisha team',
    'loan-arrear-mediation-notification' =>
        'Dear :contactName, :borrowerName provided your contacts in support of an application to join our organization, Zidisha Microfinance. :borrowerName is now :dueDays days in arrears on the loan taken from Zidisha. Can you please contact :borrowerName at :borrowerNumber and help us find out why we have not received the past due loan repayments? Please reply to this number by SMS text. Thank you, Zidisha Team',

    'loan-arrear-reminder-monthly' => 'Dear :borrowerName,

This is notification that, in accordance with the terms of the Loan Contract, we have requested mediation from one or more of the following individuals regarding your past due loan balance.
:contacts
<br>
Please send make this payment immediately following the bank deposit instructions in your Zidisha.org member account. If you are unable to make the past due payment immediately, you may use the \'Reschedule Loan\' page of your member account at Zidisha.org to propose an alternative repayment schedule to lenders.

If you do not reschedule and we do not receive the past due amount, then we will continue to contact and request mediation from members of your community. Thank you, Zidisha Team',
];
