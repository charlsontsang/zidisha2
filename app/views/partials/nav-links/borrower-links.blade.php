@if(Auth::check() && Auth::getUser()->isBorrower())
<li class="@if (Request::segment(2) == 'dashboard') active @else '' @endif "><a href="{{ route('borrower:dashboard') }}">@lang('borrower.menu.dashboard')</a></li>
@if(Auth::getUser()->getBorrower()->getLastLoanId())
    <li><a href="{{ route('loan:index', Auth::getUser()->getBorrower()->getLastLoanId()) }}">@lang('borrower.menu.loan-page')</a></li>
@endif
<li class="@if (Request::segment(2) == 'edit-profile') active @else '' @endif "><a href="{{ route('borrower:edit-profile') }}">@lang('borrower.menu.edit-profile')</a></li>
<li class="@if (Request::segment(2) == 'invite') active @else '' @endif "><a href="{{ route('borrower:invite') }}">@lang('borrower.menu.send-invites')</a></li>
<li class="@if (Request::segment(2) == 'invites') active @else '' @endif "><a href="{{ route('borrower:invites') }}">@lang('borrower.menu.your-invites')</a></li>
<li class="@if (Request::segment(2) == 'credit') active @else '' @endif "><a href="{{ route('borrower:credit') }}">@lang('borrower.menu.current-credit')</a></li>
<li class="@if (Request::segment(2) == 'history') active @else '' @endif "><a href="{{ route('borrower:history') }}">@lang('borrower.menu.payment-history')</a></li>
<li class="@if (Request::segment(1) == 'feature-criteria') active @else '' @endif "><a href="{{ route('page:loan-feature-criteria') }}">@lang('borrower.menu.feature-criteria')</a></li>
	@if(Auth::getUser()->isVolunteerMentor())
		<li><h4>@lang('borrower.menu.vm-pages')</h4></li>
		<li class="@if (Request::segment(1) == 'assigned-members') active @else '' @endif "><a href="{{ route('volunteer-mentor:get:assigned-members') }}">@lang('borrower.menu.vm-assigned-members')</a></li>
		<li class="@if (Request::segment(1) == 'volunteer-mentor-guidelines') active @else '' @endif "><a href="{{ route('page:volunteer-mentor-guidelines') }}">@lang('borrower.menu.vm-guidelines')</a></li>
		<li class="@if (Request::segment(1) == 'volunteer-mentor-code-of-ethics') active @else '' @endif "><a href="{{ route('page:volunteer-mentor-code-of-ethics') }}">@lang('borrower.menu.vm-ethics')</a></li>
		<li class="@if (Request::segment(1) == 'volunteer-mentor-faq') active @else '' @endif "><a href="{{ route('page:volunteer-mentor-faq') }}">@lang('borrower.menu.vm-faq')</a></li>
	@endif
@endif
