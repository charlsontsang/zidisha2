<li><a href="https://sites.google.com/a/zidisha.org/zidisha-staff/home/country-liaison-intern-guide">Volunteer Guide</a></li>
<li><a href="http://zidisha.org/forum/categories/volunteer-conversations.26/">Volunteer Forum</a></li>                                   
<li class="@if (Request::segment(1) == 'lenders') active @else '' @endif "><a href="{{ route('admin:lenders') }}">Look Up Lender Account</a></li>
<li class="@if (Request::segment(1) == 'borrowers') active @else '' @endif "><a href="{{ route('admin:borrowers') }}">Look Up Borrower Account</a></li>
<li><a href="#">Reset Passwords</a></li>
<li class="@if (Request::segment(1) == 'pending-disbursements') active @else '' @endif "><a href="{{ route('admin:pending-disbursements') }}">Disburse Loans</a></li>
<li class="@if (Request::segment(1) == 'repayments') active @else '' @endif "><a href="{{ route('admin:repayments') }}">Enter Repayments</a></li>
<li><a href="{{ route('admin:borrower-activation') }}">Activate Borrowers</a></li>
<li class="@if (Request::segment(1) == 'volunteer-mentors') active @else '' @endif "><a href="{{ route('admin:volunteer-mentors') }}">Manage Volunteer Mentors</a></li>
<li class="@if (Request::segment(2) == 'volunteers') active @else '' @endif "><a href="{{ route('admin:volunteers') }}">View Active Staff</a></li>
