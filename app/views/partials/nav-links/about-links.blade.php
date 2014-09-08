<li class="@if (Request::segment(1) == 'why-zidisha') active @else '' @endif "><a href="{{ route('page:why-zidisha') }}">Why Zidisha?</a></li>
<li class="@if (Request::segment(1) == 'our-story') active @else '' @endif "><a href="{{ route('page:our-story') }}">Our Story</a></li>
<li class="@if (Request::segment(1) == 'how-it-works') active @else '' @endif "><a href="{{ route('page:how-it-works') }}">How It Works</a></li>
<li class="@if (Request::segment(1) == 'trust-and-security') active @else '' @endif "><a href="{{ route('page:trust-and-security') }}">Trust &amp; Security</a></li>
<li><a href="{{ route('page:faq') }}">FAQ</a></li>
<li><a href="{{ route('page:team') }}">Team</a></li>
<li class="@if (Request::segment(1) == 'statistics') active @else '' @endif "><a href="{{ route('page:statistics') }}">Statistics</a></li>
<li class="@if (Request::segment(1) == 'press') active @else '' @endif "><a href="{{ route('page:press') }}">Press</a></li>
