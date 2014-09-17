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
                @if(Auth::check() && Auth::getUser()->isAdmin())
                <li class="dropdown dropdown-large">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        Admin <b class="caret"></b>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-large row">
                        <li class="col-sm-4">
                            <ul>
                                <li class="dropdown-header">Manage Borrowers</li>
                                <li><a href="{{ route('admin:borrowers') }}">Look Up Borrower Account</a></li>
                                <li><a href="{{ route('admin:volunteer-mentors') }}">Volunteer Mentors</a></li>
                                <li><a href="{{ route('admin:add:volunteer-mentors') }}">Add Volunteer Mentors</a></li>
                                <li><a href="{{ route('admin:borrower-activation') }}">Pending Activation</a></li>
                                <li><a href="{{ route('admin:pending-disbursements') }}">Pending Disbursements</a></li>
                                <li><a href="{{ route('admin:loans') }}">Find Loans</a></li>
                                <li><a href="{{ route('admin:repayments') }}">Enter Repayments</a></li>
                                <li><a href="{{ route('admin:loan-forgiveness:index') }}">Forgiven Loans</a></li>
                                <li><a href="{{ route('admin:get:translation-feed') }}">Translation Feed</a></li>
                            </ul>
                        </li>
                        <li class="col-sm-4">                
                            <ul>
                                <li class="dropdown-header">Manage Lenders</li>
                                <li><a href="{{ route('admin:lenders') }}">Look Up Lender Account</a></li>
                                <li><a href="{{ route('admin:volunteers') }}">Active Staff</a></li>
                                <li><a href="{{ route('admin:get:gift-cards') }}">Manage Gift Cards</a></li>
                                <li><a href="{{ route('admin:get:withdrawal-requests') }}">Withdraw Requests</a></li>
                            </ul>
                        </li>
                        <li class="col-sm-4">
                           <ul>
                                <li class="dropdown-header">Other</li>
                                <li><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                                <li><a href="{{ route('admin:translation:index') }}">Translations</a></li>
                                @if(Auth::getUser()->isAdmin())
                                    <li><a href="{{ route('admin:mail:test-mails') }}">Test Emails</a></li>
                                @endif
                                <li><a href="{{ route('admin:sms:test-sms') }}">Test SMS</a></li>
                                <li><a href="{{ route('admin:test:sift-science') }}">Test Sift Science</a></li>
                                <li><a href="{{ route('admin:countries') }}">Countries</a></li>
                                <li><a href="{{ route('admin:exchange-rates') }}">Exchange Rates</a></li>
                                <li><a href="{{ route('admin:settings') }}">Other Settings</a></li>
                                <li><a href="{{ route('logout') }}">Log Out</a></li>
                            </ul>
                        </li>
                    </ul>
                </li> 
                @elseif(Auth::check())
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
                @if(Auth::getUser()->isVolunteer())
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        Staff Links <b class="caret"></b>
                    </a>

                    <ul class="dropdown-menu">
                        @include('partials.nav-links.staff-links')
                    </ul>
                </li>
                @endif
                @else
                <li>
                    <a href="{{ route('login') }}" data-toggle="modal" data-target="#login-modal">
                        Log In
                    </a>
                </li>
                @endif
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
