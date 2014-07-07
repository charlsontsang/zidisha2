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
                zidisha
            </a>
        </div>
        <div class="collapse navbar-collapse navbar-right">
            <ul class="nav navbar-nav">
                <li><a href="{{ route('lend:index') }}">Lend</a></li>
                <li><a href="{{ route('borrow.page') }}">Borrow</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        Learn More <b class="caret"></b>
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
                        <li><a href="{{ route('admin:loans') }}">Find Loans</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        Manage Lenders <b class="caret"></b>
                    </a>

                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin:lenders') }}">Find Lenders</a></li>
                     </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        Admin Settings<b class="caret"></b>
                    </a>

                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin:countries') }}">Countries</a></li>
                    </ul>
                </li>
                @endif
            </ul>
            <form class="navbar-form navbar-left">
                @if(Auth::check())
                <a href="{{ route('logout') }}" class="btn btn-primary">
                    Log out
                </a>
                @else
                <!--a href="{{ route('logout') }}" class="btn btn-primary" data-toggle="modal" data-target="#LoginModal"-->
                <a href="{{ route('login') }}" class="btn btn-primary">
                    Log in
                </a>
                @endif
            </form>
        </div>
    </div>
</div>
