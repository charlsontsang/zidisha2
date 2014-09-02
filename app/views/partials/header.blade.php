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
                <img src="{{ '/assets/images/logo-small-black.png' }}" alt="Zidisha Logo"/>
            </a>
            <ul class="nav navbar-nav">
                <li><a href="{{ route('lend:index') }}">Browse Projects</a></li>
            </ul>
        </div>
        <div class="collapse navbar-collapse navbar-right">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        About <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        @include('partials.nav-links.about-links')
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        Community <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        @include('partials.nav-links.community-links')
                    </ul>
                </li>
                @if(Auth::check() && Auth::getUser()->getRole() != 'admin')
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        {{ Auth::getUser()->getUsername() }} <b class="caret"></b>
                    </a>

                    <ul class="dropdown-menu">
                        @if(Auth::getUser()->isLender())
                            @include('partials.nav-links.lender-links')
                        @endif
                        @if(Auth::getUser()->isBorrower())
                            @include('partials.nav-links.borrower-links')
                        @endif
                        <li><a href="{{ route('logout') }}">Log Out</a></li>
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
                        <li><a href="{{ route('admin:volunteer-mentors') }}">Volunteer Mentors</a></li>
                        <li><a href="{{ route('admin:add:volunteer-mentors') }}">Add Volunteer Mentors</a></li>
                        <li><a href="{{ route('admin:borrower-activation') }}">Pending Activation</a></li>
                        <li><a href="{{ route('admin:pending-disbursements') }}">Pending Disbursements</a></li>
                        <li><a href="{{ route('admin:loans') }}">Find Loans</a></li>
                        <li><a href="{{ route('admin:repayments') }}">Repayments</a></li>
                        <li><a href="{{ route('admin:loan-forgiveness:index') }}">Loan Forgiveness</a></li>
                        <li><a href="{{ route('admin:get:translation-feed') }}">Translation Feed</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        Manage Lenders <b class="caret"></b>
                    </a>

                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin:lenders') }}">Find Lenders</a></li>
                        <li><a href="{{ route('admin:volunteers') }}">Volunteers</a></li>
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
                        <li><a href="{{ route('logout') }}">Log Out</a></li>
                    </ul>
                </li>
                @endif
                <li>
                    @if(!Auth::check())
                    <a href="{{ route('login') }}" data-toggle="modal" data-target="#login-modal">
                        Log In
                    </a>
                    @endif
                </li>
            </ul>
            @if(Auth::check() && Auth::getUser()->isLender())
            <?php
            // TODO, refactor this
            $currentBalance = \Zidisha\Balance\TransactionQuery::create()
                ->getCurrentBalance(\Auth::id());
            $inviteCredit = \Zidisha\Balance\InviteTransactionQuery::create()
                ->getTotalInviteCreditAmount(\Auth::id());
            $lendingCredit = $currentBalance->add($inviteCredit);
            ?>
                <p id="lending-credit" class="navbar-text">
                    Lending Credit: ${{ $lendingCredit->round(2)->getAmount() }}
                </p>
            @endif
        </div>
    </div>
</div>
