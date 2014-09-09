@if(Auth::check() && Auth::getUser()->isBorrower())
<li class="@if (Request::segment(2) == 'dashboard') active @else '' @endif "><a href="{{ route('borrower:dashboard') }}">Dashboard</a></li>
<li><a href="{{ route('loan:index', Auth::getUser()->getBorrower()->getActiveLoanId()) }}">Public Loan Page</a></li>
<li class="@if (Request::segment(2) == 'edit-profile') active @else '' @endif "><a href="{{ route('borrower:edit-profile') }}">Edit Profile</a></li>
<li class="@if (Request::segment(2) == 'invite') active @else '' @endif "><a href="{{ route('borrower:invite') }}">Send Invites</a></li>
<li class="@if (Request::segment(2) == 'invites') active @else '' @endif "><a href="{{ route('borrower:invites') }}">Your Invites</a></li>
<li class="@if (Request::segment(2) == 'credit') active @else '' @endif "><a href="{{ route('borrower:credit') }}">Current Credit</a></li>
<li class="@if (Request::segment(2) == 'history') active @else '' @endif "><a href="{{ route('borrower:history') }}">Payment History</a></li>
<li class="@if (Request::segment(1) == 'feature-criteria') active @else '' @endif "><a href="{{ route('page:loan-feature-criteria') }}">How To Have Your Loan Featured</a></li>
	@if(Auth::getUser()->isVolunteerMentor())
		<h4>Volunteer Mentor Pages</h4>
		<li class="@if (Request::segment(1) == 'volunteer-mentor-guidelines') active @else '' @endif "><a href="{{ route('page:volunteer-mentor-guidelines') }}">Volunteer Mentor Guidelines</a></li>
		<li class="@if (Request::segment(1) == 'volunteer-mentor-code-of-ethics') active @else '' @endif "><a href="{{ route('page:volunteer-mentor-code-of-ethics') }}">Volunteer Mentor Code of Ethics</a></li>
		<li class="@if (Request::segment(1) == 'volunteer-mentor-faq') active @else '' @endif "><a href="{{ route('page:volunteer-mentor-faq') }}">Volunteer Mentor FAQ</a></li>
	@endif
@endif