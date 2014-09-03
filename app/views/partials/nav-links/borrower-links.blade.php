@if(Auth::check() && Auth::getUser()->isBorrower())
<li><a href="{{ route('borrower:dashboard') }}">Dashboard</a></li>
<li><a href="{{ route('loan:index', Auth::getUser()->getBorrower()->getActiveLoanId()) }}">Public Loan Page</a></li>
<li><a href="{{ route('borrower:edit-profile') }}">Edit Profile</a></li>
<li><a href="{{ route('borrower:invite') }}">Send Invites</a></li>
<li><a href="{{ route('borrower:invites') }}">Your Invites</a></li>
<li><a href="{{ route('borrower:credit') }}">Current Credit</a></li>
<li><a href="{{ route('borrower:history') }}">Payment History</a></li>
@endif