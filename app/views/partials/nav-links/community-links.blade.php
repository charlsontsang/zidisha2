<li class="@if (Request::segment(1) == 'project-updates') active @else '' @endif "><a href="{{ route('project-updates') }}">Project Updates</a></li>
<li><a href="{{ route('lender:groups') }}">Lending Groups</a></li>
<li><a href="https://www.zidisha.org/forum/">Forum</a></li>
<li class="@if (Request::segment(1) == 'volunteer') active @else '' @endif "><a href="{{ route('page:volunteer') }}">Volunteer</a></li>
<li><a href="http://p2p-microlending-blog.zidisha.org/">Blog</a></li>