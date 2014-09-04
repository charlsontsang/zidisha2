<?php
return [
    'title' => [
        'instructions-page' => 'Instructions',
        'profile-page' => 'Complete your profile',
        'application-page' => 'Loan application',
        'publish-page' => 'Review and publish',
    ],
    'publish-loan' => [
        'amount-requested'                      => 'Amount Requested',
        'maximum-interest-rate'                 => 'Maximum Interest Rate',
        'monthly-repayment-amount'              => 'Monthly Repayment Amount',
        'repayment-period'                      => 'Repayment Period',
        'maximum-interest-and-transaction-fees' => 'Maximum Interest and Transaction Fees',
        'total-repayment-due-date'              => 'Total Repayment Due Date',
        'loan-confirmation-instructions'        => 'The following payment schedule is generated to illustrate the payments you are committing to make should the loan you proposed be financed at the maximum interest rate. Please review it carefully to ensure that the repayment amounts and dates are what you intended to propose, and that you will be able to make the below scheduled repayments without difficulty.',
        'loan-confirmation'                     => 'You may modify your loan application by clicking the "Go Back and Edit" button. Once you click "Confirm and Publish", your application will be posted for funding by lenders.',
        'table'                                 => [
            'due-date'          => 'Due Date (Number Of months after disbursement Date)',
            'repayment-due'     => 'Repayment Due (:currencyCode)',
            'balance-remaining' => 'Balance Remaining',
            'total-repayment'   => 'Total Repayment',
        ]
    ],
    'current-credit' => [
        'title' => 'Current Credit Limit',
        'first-loan' => '<i>This is the standard credit limit for the first loan raised through Zidisha.</i><br/><br/><br/>',
        'repaid-late' => '<i>You are not eligible for a credit limit increase because your most recent loan was not repaid on time.  In order to qualify for an increase in maximum loan size, you must repay your next loan on time while maintaining an on-time repayment rate for monthly installments of at least :minimumRepaymentRate%.</i>
<br/><br/><br/>',
        'time-insufficient' => '<i>In order to qualify for an increase in maximum loan size, you must hold the current loan for at least :timeThreshold months and maintain an on-time repayment rate for monthly installments of at least :minimumRepaymentRate%.</i>
<br/><br/><br/>',
        'repayment-rate-insufficient' => '<i>Your current on-time repayment rate for monthly installments is :borrowerRepaymentRate%. In order to qualify for an increase in maximum loan size, you must improve your on-time repayment rate by making future monthly repayment installments on time.  Once your on-time repayment rate for monthly installments reaches :minimumRepaymentRate%, you will become eligible for a loan size increase.</i>
<br/><br/><br/>',
        'repayment-rate-sufficient' => '<i>Your current on-time repayment rate for monthly installments is :borrowerRepaymentRate%. In order to remain eligible for an increase in maximum loan size, you must continue to make at least :minimumRepaymentRate% of your monthly repayment installments on time.</i>
<br/><br/><br/>',
        'beginning' => 'This page shows your current credit limit, or the maximum amount you could raise if you were to post a new loan application today. <br/><br/>Please note that your current credit limit is based the amounts you have repaid in the past, and on the on-time repayment performance of each weekly installment due. From time to time, Zidisha may also offer bonus credits for positive contributions to our community, or change the amounts by which credit limits increase for a given level of performance.<br/><br/><br/>

<strong>Your current credit limit is :currentCreditLimit.</strong>

<br/><br/><br/>
Here is how that credit limit was determined:<br/><br/>
1. Base credit limit: :baseCreditLimit<br/><br/>',
        'invite-credit' => '2. <a href=\':myInvites\'>Bonus Credit For Inviting New Members:</a> :inviteCredit<br/><br/><br/>',
        'volunteer-mentor-credit' => '3. Bonus Credit For Volunteer Mentor Assigned Members Who Are Current With Repayments</a>: :volunteerMentorCredit<br/><br/><br/>',
        'end' => '<strong>Total Credit Limit Earned: :currentCreditLimit</strong><br/>
<br/><br/><br/>
In order to increase your maximum loan size, you must:<br/><br/>
<ul><li>Maintain a :minimumRepaymentRate% on-time repayment rate for all monthly installments since joining Zidisha.</li>
<li>Make the final repayment installment of the current loan on time.</li>
<li>Hold the current loan for at least :timeThreshold months.</li>
<li>Distribute your repayments over a series of regular installments. You may not qualify for a loan size increase if you repay more than $100 and more than 10% of your loan in any 30-day period.</li></ul>
</ul><br/><br/>
The current credit limit increase progression for Zidisha members who fulfill the above criteria is as follows:<br/><br/>
	<p>
	    :firstLoanVal
	    :nxtLoanvalue
	</p>
<br/>',
    ],
    'instructions' => [
        'intro' => 'Zidisha provides a platform whereby our members can raise loans by proposing mutually beneficial terms to lenders.  Lenders choose from many competing applications, and your loan will only be funded if it succeeds in appealing to lenders.', 
        'deadline' => 'If your loan application is not fully funded in :deadline days, it will expire and any bids raised will be returned to lenders. You may then try again with a new application.',
        'tips' => 'Your loan is most likely to be funded if you make sure to follow these three tips:',
        'tip1' => 'Make sure your photo describes your business.  Use a photo of yourself working in your business or showing your business products, not a passport photo.  Also make sure to smile and show your face clearly in the photo.',
        'tip2' => 'Include something interesting you have done in your life or business, or a special hobby or pastime, in your "About Me" and "About My Business" descriptions.',
        'tip3' => 'Use a loan title that tells lenders what you will do or purchase with the loan funds, and make sure that you include a clear explanation in your "Use of Loan" description.',
        'more-tips' => 'For more tips on how to ensure your loan is funded quickly, see <strong><a href=":link">How To Have Your Loan Featured</a></strong>.',
    ],
    'next' => 'Next',
];
