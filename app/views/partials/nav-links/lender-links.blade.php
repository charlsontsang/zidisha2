<li class="@if (Request::segment(2) == 'dashboard') active @else '' @endif "><a href="{{ route('lender:dashboard') }}">Dashboard</a></li>
<li><a href="{{ route('lender:loans') }}">Your Loans</a></li>
<li class="@if (Request::segment(2) == 'following') active @else '' @endif "><a href="{{ route('lender:following') }}">Following</a></li>
<li><a href="{{ route('lender:public-profile', Auth::getUser()->getId()) }}">View Profile</a></li>
<li class="@if (Request::segment(3) == 'edit') active @else '' @endif "><a href="{{ route('lender:edit-profile') }}">Edit Profile</a></li>
<li class="@if (Request::segment(2) == 'preferences') active @else '' @endif "><a href="{{ route('lender:preference') }}">Account Preferences</a></li>
{{--<li><a href="{{ route('lender:gift-cards') }}">Gift Cards</a></li>--}}
<li class="@if (Request::segment(2) == 'gift-cards') active @else '' @endif "><a href="{{ route('lender:gift-cards:track') }}">Track Gift Cards</a></li>
<li><a href="{{ route('lender:invite') }}">Invite Friends</a></li>
<li class="@if (Request::segment(2) == 'history') active @else '' @endif "><a href="{{ route('lender:history') }}">Transaction History</a></li>
<li class="@if (Request::segment(2) == 'funds') active @else '' @endif "><a href="{{ route('lender:funds') }}">Transfer Funds</a></li>
<li class="@if (Request::segment(2) == 'auto-lending') active @else '' @endif "><a href="{{ route('lender:auto-lending') }}">Autolending</a></li>