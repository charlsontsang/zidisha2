<div class="navbar navbar-default navbar-static-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ route('home') }}">
                <span class="sr-only">Zidisha</span>
                <img src="{{ '/assets/images/logo-small-dark.jpg' }}" alt="Zidisha Logo"/>
            </a>
        </div>
        <div class="collapse navbar-collapse navbar-right">
            <ul class="nav navbar-nav">
                <li><a href="{{ route('lend:index') }}">Lend</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        About <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Member Updates &amp; Photos</a></li>
                        <li><a href="{{ route('page:our-story') }}">Our Story</a></li>
                        <li><a href="{{ route('page:why-zidisha') }}">Why Zidisha?</a></li>
                        <li><a href="{{ route('page:how-it-works') }}">How It Works</a></li>
                        <li><a href="{{ route('page:trust-and-security') }}">Trust &amp; Security</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Team</a></li>
                        <li><a href="#">Statistics</a></li>
                        <li><a href="{{ route('page:press') }}">Press</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        Community <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Project Updates</a></li>
                        <li><a href="{{ route('lender:groups') }}">Lending Groups</a></li>
                        <li><a href="#">Forum</a></li>
                        <li><a href="#">Volunteer</a></li>
                        <li><a href="http://p2p-microlending-blog.zidisha.org/">Blog</a></li>
                    </ul>
                </li>
                @if(Auth::check() && Auth::getUser()->getRole() != 'admin')
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        My Account <b class="caret"></b>
                    </a>

                    <ul class="dropdown-menu">
                        @if(Auth::getUser()->isLender())
                        <li><a href="{{ route('lender:dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('lender:public-profile', Auth::getUser()->getUsername()) }}">View My
                                Public Profile</a></li>
                        <li><a href="{{ route('lender:edit-profile') }}">Edit Profile</a></li>
                        <li><a href="{{ route('lender:preference') }}">Account Preference</a></li>
                        <li><a href="{{ route('lender:gift-cards') }}">Gift Cards</a></li>
                        <li><a href="{{ route('lender:gift-cards:track') }}">Track My Gift Cards</a></li>
                        <li><a href="{{ route('lender:invite') }}">Invite Your Friends</a></li>
                        <li><a href="{{ route('lender:history') }}">Transaction History</a></li>
                        <li><a href="{{ route('lender:funds') }}">Add or Withdraw Funds</a></li>
                        @endif
                        @if(Auth::getUser()->isBorrower())
                        <li><a href="{{ route('borrower:dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('borrower:public-profile', Auth::getUser()->getUsername()) }}">View Public
                                Profile</a></li>
                        <li><a href="{{ route('borrower:edit-profile') }}">Edit Profile</a></li>
                            @if(Auth::user()->getBorrower()->hasActiveLoan())
                                <li><a href="{{ route('borrower:loan-information', ['loanId' => Auth::user()->getBorrower()->getActiveLoanId() ]) }}">My Loan</a></li>
                            @endif
                        <li><a href="{{ route('borrower:history') }}">Transaction History</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                @if(Auth::check() && Auth::getUser()->isAdmin())
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        Manage Borrowers <b class="caret"></b>
                    </a>

                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin:borrowers') }}">Find Borrowers</a></li>
                        <li><a href="{{ route('admin:borrower-activation') }}">Pending Activation</a></li>
                        <li><a href="{{ route('admin:pending-disbursements') }}">Pending Disbursements</a></li>
                        <li><a href="{{ route('admin:loans') }}">Find Loans</a></li>
                        <li><a href="{{ route('admin:get:translation-feed') }}">Translation Feed</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        Manage Lenders <b class="caret"></b>
                    </a>

                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin:lenders') }}">Find Lenders</a></li>
                        <li><a href="{{ route('admin:get:gift-cards') }}">Gift Cards</a></li>
                        <li><a href="{{ route('admin:get:withdrawal-requests') }}">Withdraw Requests</a></li>
                     </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        Admin Settings<b class="caret"></b>
                    </a>

                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin:translation:index') }}">Translations</a></li>
                        <li><a href="{{ route('admin:mail:test-mails') }}">Test mails</a></li>
                        <li><a href="{{ route('admin:sms:test-sms') }}">Test sms</a></li>
                        <li><a href="{{ route('admin:countries') }}">Countries</a></li>
                        <li><a href="{{ route('admin:settings') }}">Other Settings</a></li>
                    </ul>
                </li>
                @endif
                <li>
                    @if(Auth::check())
                    <a href="{{ route('logout') }}">
                        Log out
                    </a>
                    @else
                    <a href="{{ route('login') }}" data-toggle="modal" data-target="#login-modal">
                        Log in
                    </a>
                    @endif
                </li>
            </ul>
        </div>
    </div>
</div>
