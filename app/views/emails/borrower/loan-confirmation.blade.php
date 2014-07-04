Dear {{ $borrower->getName() }},<br/><br/>
Congratulations!  Your loan application has been posted for funding.  Click <a
    href="{{ route('loan:index', $loan->getId()) }}">here</a> to view your loan application page.<br/><br/>
Please note that your application will be posted for a maximum of %deadline% days, or until it is fully funded and you choose to accept the bids raised. You may edit your loan application page at any time using the
<a href="%loanappliclink%">Loan Application</a> page.<br/><br/>
Best of luck in your endeavor,<br/><br/>
The Zidisha Team
