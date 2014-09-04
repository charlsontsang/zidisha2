<?php

return [
    'profile'    => [
        'karma' => 'Karma goes up with good deeds like posting comments, inviting new lenders and helping to develop our lending group community.',
    ],
    'loans'      => [
        'funds-uploaded'                  => 'The total amount of funds you have uploaded into your account as lending credit. Does not include loan repayments credited to your account.',
        'loans-outstanding'               => 'The portion of US dollar amounts you have lent which is still outstanding with the borrowers (not yet repaid). This amount does not include any interest which is due for the loans, and its value is not adjusted for credit risk or exchange rate fluctuations.',
        'lending-credit-available'        => 'The current balance of credit available for lending, composed of lender fund uploads and repayments received, which have not been withdrawn or bid on new loans. Does not include amounts in your Lending Cart.',
        'amount-repaid-active-loans'      => 'This is the amount that has been repaid into your lending account for this loan, including interest and adjusted for currency exchange rate fluctuations.',
        'amount-outstanding-active-loans' => 'The portion of US dollar amounts you have lent which is still outstanding with the borrowers (not yet repaid). This amount does not include any interest which is due for the loans, and its value is not adjusted for credit risk or exchange rate fluctuations.',
        'amount-repaid-completed-loans'   => 'This is the amount that has been repaid into your lending account for this loan, including interest and adjusted for currency exchange rate fluctuations.',
        'net-change-completed-loans'      => 'This is the amount by which this loan increased or decreased the total value of your loan fund. It is the difference between the amount you originally paid to fund this loan, and the total amount that was returned to your account after currency fluctuations, interest and any writeoff or forgiveness of outstanding principal.'
    ],
    'pages'      => [
        'loan-money-raised' => 'The cumulative US Dollar amount of loans disbursed.',
        'loan-projects-funded' => 'The cumulative number of individual loans funded.',
        'loan-money-raised-filtered' => 'The total US Dollar amount of loans disbursed in the selected time period and location.',
        'loan-projects-funded-filtered' => 'The number of individual loans funded in the selected time period and location.',
        'average-lender-interest' => 'The average lender interest rate of all loans fully funded by lenders and accepted by borrowers, weighted by the dollar amount of each lender\'s share. Interest is expressed as a flat percentage of loan principal per year the loan is held.',
        'principal-repaid' => 'The principal (not including interest) that has already been repaid to lenders for loans disbursed in the selected time period and location, expressed as a dollar amount and as a percentage of the amount disbursed.',
        'principal-repaid-on-time' => 'The principal (not including interest) still held by borrowers who are current or less than 30 days and $10 past due with their scheduled repayment installments, expressed as a dollar amount and as a percentage of the amount disbursed.',
        'principal-repaid-due' => 'The principal (not including interest) still held by borrowers who are more than 30 days and $10 past due with their scheduled repayment installments, expressed as a dollar amount and as a percentage of the amount disbursed.',
        'principal-forgiven' => 'The principal (not including interest) that lenders have elected not to accept as repayments for humanitarian reasons.',
        'principal-written-off' => 'The principal (not including interest) that has been classified as written off. Zidisha classifies as written off all amounts that have not been repaid six months after a loan\'s final repayment installment due date, and all loans for which the borrower has not made a payment in over six months.  Writing off a loan is a reporting convention, and does not necessarily mean collection efforts stop or that it will not be repaid to lenders.'
    ]
];
