<?php

return [
    'accept-bids'                => [
        'instructions' => 'Please review the following repayment schedule carefully. By clicking the "Accept Bids" button below, you will be entering into a legal contract to repay the loan according to this schedule.',
        'schedule'     => 'The following payment schedule is generated to illustrate the payments you are committing to make should the loan you proposed be financed at the maximum interest rate. Please review it carefully to ensure that the repayment amounts and dates are what you intended to propose, and that you will be able to make the below scheduled repayments without difficulty.  <br/><br/>You may modify your loan application by clicking the "Go Back and Edit" button.  Once you click "Confirm and Publish", your application will be posted for funding by lenders.',
        'default-note' => 'Please enter any special instructions for your loan disbursement here (optional)',
        'submit'       => 'Accept Bids',
    ],
    'requested-amount'                      => 'Amount Requested',
    'maximum-interest-rate'                 => 'Maximum Interest Rate',
    'disbursed-amount'                      => 'Loan Principal Disbursed',
    'monthly-repayment-amount'              => 'Monthly Repayment Amount',
    'weekly-repayment-amount'               => 'Weekly Repayment Amount',
    'maximum-interest-and-transaction-fees' => 'Maximum Interest and Transaction Fees',
    'repayment-period'                      => 'Repayment Period',
    'original-repayment-period'             => 'Original Repayment Period',
    'final-lender-interest-rate'            => 'Final Interest Rate Bid By Lenders',
    'service-fee-rate'                      => 'Service Fee',
    'registration-fee'                      => 'One-Time Registration Fee',
    'total-interest-and-fees'               => 'Total Interest and Transaction Fees',
    'monthly-interest-rate'                 => ':interestRate % annual rate for :period months',
    'weekly-interest-rate'                  => ':interestRate % annual rate for :period weeks',
    'total-amount'                          => 'Total Amount (Including Interest and Transaction Fee) to be Repaid',
    'total-amount-due'                      => 'Total Repayment Due',
    'total-amount-due-date'                 => 'Total Repayment Due Date',
    'expires-at'                            => 'Expiration Date',
    'repayment-schedule'                    => [
        'due-date'          => 'Due Date (Number Of months after disbursement Date)',
        'repayment-due'     => 'Repayment Due (:currencyCode)',
        'balance-remaining' => 'Balance Remaining',
        'total-repayment'   => 'Total Repayment',
    ],
    'reschedule'                 => [
        'title'                          => 'Reschedule Loan',
        'description'                    => 'This page allows you to propose a change to your repayment schedule. If you choose to lengthen your repayment period, you will be asked to pay interest for the additional time the loan will be held, at the same annual interest rate as is applied to your current loan repayment schedule. If you shorten your repayment period, the interest you will be asked to pay will be reduced.',
        'installment-amount'             => 'New Installment Amount',
        'installment-amount-description' => 'Please enter the new amount you wish to pay each month. As the loan must be fully repaid within :maxPeriod months, the minimum monthly installment is :minInstallmentAmount.',
        'reason'                         => 'Reason',
        'reason-description'             => 'Please explain here the reason for the change in the repayment schedule (as much as you are comfortable sharing). This will be posted on your public loan profile page.',
        'note'                           => '<strong>Please note</strong>: You will have the chance to review your new repayment schedule before finalizing the change.',
        'submit'                         => 'Submit',
        'current-schedule'               => 'Current Schedule',
        'new-schedule'                   => 'New Schedule',
        'confirmation-note'              => 'IMPORTANT NOTE: Your new repayment schedule is not yet accepted. Please review carefully and click ":cancel" in order to make any changes you desire. Once you are satisfied with the new repayment schedule, click ":confirm" in order to accept it.',
        'confirm'                        => 'Confirm',
        'cancel'                         => 'Go Back And Edit',
    ],
    'fully-funded'               => [
        'message' => 'Your Loan is fully funded, and disbursement is pending.'
    ],
    'active'                     => [
        'next-installment' => 'Next Installment'
    ],
    'partials'                   => [
        'expected-payments' => 'Expected Payments',
        'actual-payments'   => 'Actual Payments',
        'feedback'          => 'Feedback',
    ],
    'expired'                    => [
        'expire-message' => 'Your Loan has been Expired.'
    ],
    'canceled'                   => [
        'cancel-message' => 'Your Loan has been Canceled.'
    ],
    'no-loan'                    => [
        'message' => 'You have no active loan. Click <a href=":link">here</a> to apply for a loan.'
    ],
    'progress' => [
        'funded'       => 'Funded',
        'still-needed' => 'Still Needed',
        'days-left'    => 'Days Left',
        'hours-left'   => 'Hours Left',
        'minutes-left' => 'Minutes Left',
        'seconds-left' => 'Seconds Left',
        'expired'      => 'Expired',
    ],
];
