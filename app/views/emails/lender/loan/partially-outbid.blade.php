Hi there,
<br/><br/>
This is a notification that USD {{ $outbidAmount }} of your bid to fund USD {{ $bidAmount }} of the loan for
<a href="{{ $borrowerLink }}" target='_blank'>{{ $borrowerName }}</a> at {{ $bidInterestRate }}% interest
has been outbid by another lender who proposed a lower interest rate.
The remaining value of your bid for this loan is USD {{ $acceptedAmount }}.
The amount outbid of USD {{ $outbidAmount }} has been returned to your lender account,
and you may use it to fund another loan or to bid again on this one.
<br/><br/>
Loan bids may be partially or fully outbid when the total value of lender bids exceeds the amount needed for the loan.
In these cases, only the amount originally requested by the borrower is accepted,
and bids at the lowest interest rates are retained.
You may bid again on <a href="{{ $loanLink }}" target='_blank'>{{ $borrowerName }}</a>'s loan by proposing a lower interest rate.
<br/><br/>
Best wishes,<br/><br/>
The Zidisha Team
