<?php
return [
    'not-eligible'                              => 'You are not currently eligible to send invite credits to new members, because',
    'not-eligible-repayRate'                    => 'your on-time repayment rate is below the minimum required to qualify for participation in the invite program.',
    'not-eligible-invitee-repayRate'            => 'less than 90% of your current invitees are on time with their repayments.',
    'not-eligible-invitee-quota'                => 'you have reached the maximum of 100 total invitees.',
    'not-eligible-no-payments'                  => "haven't made any payments yet.",
    'not-eligible-max-invites-without-payment'  => 'you already have :maxInviteesWithoutPayment invitees who have not yet made payments.
To become eligible to issue more invite credits, you may wait for your current invitees to begin to make repayments, or if any of your invitees do not wish to raise loans, you may remove them from your invitee list to make room for new invitees.<br/><br/>
You may view the status of your current invitees and remove inactive invitees here: <a href=\':myInvites\'>My Invited Members</a>',
    'invites-message'                           => 'Here is a list of all members who have accepted your email invite to join Zidisha. For each member you have invited, as long as that member maintains a :minRepaymentRate% on-time repayment rate, your maximum credit limit will increase by a bonus amount of :borrowerInviteCredit.',
    'success-rate'                              => 'Invited Member Success Rate',
    'success-rate-tooltip'                      => 'This is the percentage of members you have invited who are meeting the on-time repayment rate standard.',
    'bonus-earned'                              => 'Total Bonus Earned',
    'bonus-earned-tooltip'                      => 'This is the total bonus for inviting new members that will be added to the maximum credit limit when you post your next loan application.  It is the number of your invited members who meet the on-time repayment rate standard, times the current invite bonus amount for your country.',
    'name'                                      => "Name",
    'email'                                     => "Invitee email address",
    'borrower-name'                             => 'Your name',
    'borrower-email'                            => 'Your email address',
    'subject'                                   => 'Enter Your Invite Message Title',
    'message'                                   => 'Enter Your Invite Message',
    'send-invite'                               => 'Send Invite',
    'status'                                    => 'Status',
    'repayment-rate'                            => 'On-Time Repayment Rate',
    'bonus-credit'                              => 'Bonus Credit Earned',
    'invite-not-accepted'                       => 'Invite Not Yet Accepted',
    'invite-accepted'                           => 'Invite Accepted',
    'application-not-submitted'                 => 'Application Not Yet Submitted',
    'application-pending-verification'          => 'Application Pending Verification',
    'application-decline'                       => 'Declined',
    'application-pending-review'                => 'Application Pending Review',
    'no-loan'                                   => 'Activated, No Loan Yet',
    'fundRaising-loan'                          => 'Fundraising Loan',
    'repaying-on-time'                          => 'Repaying On Time',
    'past-due'                                  => 'Past Due',
    'delete'                                    => 'Remove this invitee',
    'flash'                                     => [
        'invite-success' => 'You have successfully invited :email',
        'not-eligible'   => 'You are not currently eligible to send invite credits to new members.',
        'invite-deleted' => 'Successfully deleted invite :email.',
    ]
];