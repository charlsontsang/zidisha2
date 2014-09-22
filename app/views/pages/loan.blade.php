@extends('layouts.master')

@section('page-title')
    {{ $borrower->getName() }}
@stop

@section('content-top')
    <div class="loan-titlebar">
        <span id="country" class="text-light">
            {{ $borrower->getCountry()->getName() }}
        </span>
        <p class="alpha">
            {{ $loan->getSummary() }}
        </p>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-sm-8 loan-body">
        
        <div class="pull-left profile-image" href="{{ route('loan:index', $loan->getId()) }}"
            style="background-image:url({{ $borrower->getUser()->getProfilePictureUrl('large-profile-picture') }})" width="100%" height="450px">
        </div>
        <!--
        <img src="{{ $borrower->getUser()->getProfilePictureUrl('large-profile-picture') }}" width="100%">
        -->

        <br/>
        <br/>

        <ul class="nav nav-tabs nav-justified" role="tablist">
            <li class="active"><a href="#about" role="tab" data-toggle="tab">About</a></li>
            <li>
                <a href="#discussion" role="tab" data-toggle="tab">
                    Discussion <span class="badge badge-danger">{{ $commentCount }}</span>
                </a>
            </li>
            @if($loan->isDisbursed())
            <li><a href="#repayment" role="tab" data-toggle="tab">Repayment</a></li>
            @endif
        </ul>

        <div id="tab-content" class="tab-content">
            <div class="tab-pane fade active in" id="about">
                
                <div class="loan-section">
                    <div class="loan-section-title">
                        <span class="text-light">Borrower</span>
                    </div>
                    <div class="loan-section-content">
                        <div class="row">
                            <div class="col-sm-6">
                                <strong>{{{ $borrower->getName() }}}</strong>
                                <br/>
                                <strong>
                                    <a href="https://www.google.com/maps/place/{{ $borrower->getProfile()->getCity() }},+{{ $borrower->getCountry()->getName() }}/" target="_blank">{{ $borrower->getProfile()->getCity() }}</a>,&nbsp;{{ $borrower->getCountry()->getName() }}
                                </strong>
                                
                                @if($invitedBy)
                                <br/>
                                Invited By:
                                <strong>
                                 @if($invitedBy->getLastLoanId())
                                    <a href="{{ route('loan:index', $invitedBy->getLastLoanId()) }}">
                                     {{ $invitedBy->getName() }}</a>
                                 @else
                                     {{ $invitedBy->getName() }}
                                @endif
                                </strong>
                                @endif
                                
                                @if($volunteerMentor)
                                <br/>
                                Volunteer Mentor:
                                <strong>
                                 @if($volunteerMentor->getLastLoanId())
                                    <a href="{{ route('loan:index', $volunteerMentor->getLastLoanId()) }}">
                                     {{ $volunteerMentor->getName() }}</a>
                                 @else
                                     {{ $volunteerMentor->getName() }}
                                @endif
                                </strong>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                Followers: 
                                <strong>{{ $followersCount }}</strong>
                                <br/>

                                <div id="follow-link">
                                @if(Auth::check())
                                    @if(Auth::user()->isLender())
                                    <a
                                        href="{{ route('lender:follow', $borrower->getId()) }}"
                                        class="followBorrower"
                                        style="{{ $follower ? 'display:none' : '' }}"
                                        data-follow="follow"
                                        data-toggle="tooltip">
                                        Follow {{ $borrower->getFirstName() }}
                                    </a>
                                    
                                        @include('lender.follow.follower', [
                                            'lender' => Auth::user()->getLender(),
                                            'follower' => $follower,
                                        ])
                                    @endif
                                @else
                                    @lang('lender.follow.login', ['name' => $borrower->getFirstName(), 'link' => route('login')])                                           
                                @endif
                                </div>

                                @if ($totalFeedback > 0)
                                    Feedback Rating:{{ BootstrapHtml::tooltip('borrower.tooltips.loan.feedback-rating') }} 
                                    <strong>{{ $feedbackRating }} % Positive ({{ $totalFeedback }})</strong>
                                    <br/>
                                @endif

                                @if($displayFeedbackComments)
                                    <p><a href="#feedback">View Lender Feedback</a></p>
                                @endif

                                On-Time Repayments:{{ BootstrapHtml::tooltip('borrower.tooltips.loan.on-time-repayments') }}
                                <strong>TODO</strong>
                                <br/>
                                
                                @if($previousLoans != null)
                                <div class="DemoBS2">
                                    <!-- Toogle Buttons -->
                                    <a class="previous-loans" id="toggle-btn"
                                       data-toggle="collapse" data-target="#toggle-example">View Previous Loans</a>

                                    <div id="toggle-example" class="collapse">
                                        @foreach($previousLoans as $oneLoan)
                                        <p><a href="{{ route('loan:index', $oneLoan->getId()) }}">{{ $oneLoan->getUsdAmount() }}
                                                {{ $oneLoan->getAppliedAt()->format('d-m-Y') }}
                                                {{-- TODO $oneLoan->getAcceptedAt()->format('d-m-Y')
                                                $oneLoan->getExpiredDate()->format('d-m-Y')
                                                TODO change Amount to disbursedAmount in USD
                                                --}}
                                            </a>
                                        </p>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <hr/>

                <div class="loan-section">
                    <div class="loan-section-title">
                        <span class="text-light">This Loan</span>
                    </div>
                    <div class="loan-section-content">
                        <div class="visible-xs">
                            @if($loan->isExpired())
                            <span class="label label-default">
                                Loan application expired
                            </span>
                            @include('partials/loan-progress', [ 'loan' => $loan ])
                            @endif

                            @if($loan->isCanceled())
                            <span class="label label-default">
                                Loan application canceled
                            </span>
                            <br/>
                            @endif
                        
                            @if($loan->isFunded())
                            <span class="label label-default">
                                Pending disbursement
                            </span>
                            <br/>
                            @endif

                            @if($loan->isDisbursed())
                                @include('loan.partials.repaid-bar', compact('loan'))
                            <br/>
                            @endif
                            
                            <br/>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                @if($loan->isDisbursed())
                                    Disbursed Amount: 
                                    <strong>{{ $disbursedAmount }}</strong>
                                    <br/>
                                    Date Disbursed: 
                                    <strong>{{ $loan->getDisbursedAt()->format('M j, Y') }}</strong>
                                    <br/>
                                    Repayment period:{{ BootstrapHtml::tooltip('borrower.tooltips.loan.repayment-period') }}
                                    <strong>{{ $loan->getPeriod() }}
                                    @if($loan->isWeeklyInstallment())
                                        weeks
                                    @else
                                        months
                                    @endif
                                    </strong>
                                @else
                                    Amount requested:
                                    <strong>{{{ $loan->getUsdAmount() }}}</strong>
                                    <br/>
                                    @if($loan->isOpen())
                                    Still needed:
                                    <strong>{{{ $loan->getStillNeededUsdAmount() }}}</strong>
                                    <br/>
                                    Application expires:
                                    <strong>{{{ $loan->getExpiresAt()->format('M j, Y') }}}</strong>
                                    @endif
                                @endif
                            </div>
                            @if($loan->isDisbursed())
                            <div class="col-sm-6">     
                                Lender interest:{{ BootstrapHtml::tooltip('borrower.tooltips.loan.lender-interest') }}
                                <strong>{{ $lenderInterest }} ({{ $loan->getLenderInterestRate() }}%)</strong>
                                <br/>
                                Service fee:{{ BootstrapHtml::tooltip('borrower.tooltips.loan.service-fee') }}
                                <strong>{{ $serviceFee }} ({{ $loan->getServiceFeeRate() }}%)</strong>
                                <br/>
                                Total cost of loan:{{ BootstrapHtml::tooltip('borrower.tooltips.loan.total-cost-of-loan') }}
                                <strong>{{ $totalAmount }}</strong>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <hr/>
                
                <div class="loan-section">
                    <div class="loan-section-title">
                        <span class="text-light">Story</span>
                    </div>
                    <div class="loan-section-content">
                        <h5 class="alpha">About Me</h5>

                        <p>{{ $borrower->getProfile()->getAboutMe() }}</p>
                        
                        @if(Auth::check() && Auth::getUser()->isAdmin())
                            <a href="{{ route('admin:get-translate', $loan->getId()) }}#about-me">Edit translation</a>
                        @endif

                        @if($borrower->getProfile()->getAboutMeTranslation())
                        <div>
                            <p class="text-right">
                                <a href="#" data-toggle="collapse" data-target="#toggle-aboutMe" data-toggle-text="Hide original language">
                                    Display posting in original language
                                </a>
                            </p>

                            <div id="toggle-aboutMe" class="collapse">
                                <p>
                                    {{ $borrower->getProfile()->getAboutMeTranslation() }}
                                </p>
                            </div>
                        </div>
                        @endif

                        <h5>My Business</h5>

                        <p>{{ $borrower->getProfile()->getAboutBusiness() }}</p>
                        
                        @if(Auth::check() && Auth::getUser()->isAdmin())
                            <a href="{{ route('admin:get-translate', $loan->getId()) }}#about-business">Edit translation</a>
                        @endif

                        @if($borrower->getProfile()->getAboutBusinessTranslation())
                        <div>
                            <p class="text-right">
                                <a  href="#" data-toggle="collapse" data-target="#toggle-aboutBusiness" data-toggle-text="Hide original language">
                                    Display posting in original language
                                </a>
                            </p>

                            <div id="toggle-aboutBusiness" class="collapse">
                                <p>
                                    {{ $borrower->getProfile()->getAboutBusinessTranslation() }}
                                </p>
                            </div>
                        </div>
                        @endif

                        <h5>Loan Proposal</h5>

                        <p class="{{ $loan->getProposalTranslation() ? '' : 'omega' }}">
                            {{ $loan->getProposal() }}
                        </p>
                        
                        @if(Auth::check() && Auth::getUser()->isAdmin())
                            <a href="{{ route('admin:get-translate', $loan->getId()) }}#proposal">Edit translation</a>
                        @endif

                        @if($loan->getProposalTranslation())
                        <div>
                            <p class="text-right">
                                <a  href="#" data-toggle="collapse" data-target="#toggle-proposal" data-toggle-text="Hide original language">
                                    Display posting in original language
                                </a>
                            </p>

                            <div id="toggle-proposal" class="collapse">
                                <p class="omega">
                                    {{ $loan->getProposalTranslation() }}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                @if($displayFeedbackComments)
                    <hr/>
                    
                    <div id="feedback">
                        <div class="loan-section">

                            <div class="loan-section-title">
                                <span class="text-light">Feedback</span>
                            </div>

                            <div class="loan-section-content">
                                @if(count($loanFeedbackComments))
                                    @foreach($loanFeedbackCounts as $rating => $count)
                                        <?php
                                        $labelClass = 'default';
                                        if ($rating == \Zidisha\Comment\LoanFeedbackComment::POSITIVE) {
                                            $labelClass = 'success';
                                        } elseif ($rating == \Zidisha\Comment\LoanFeedbackComment::NEGATIVE) {
                                            $labelClass = 'danger';
                                        }
                                        ?>
                                        <span class="label label-{{ $labelClass }}">
                                            @lang('borrower.loan.feedback.' . $rating)
                                        </span>
                                        &nbsp;
                                        {{ $count }}
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                    @endforeach
                                @else
                                    No feedback yet.
                                @endif
                            </div>
                        </div>

                        @include('partials.comments.comments', [
                            'comments' => $loanFeedbackComments,
                            'receiver' => $loan,
                            'controller' => 'LoanFeedbackController',
                            'canPostComment' => $canPostFeedback,
                            'canReplyComment' => $canReplyFeedback
                        ])
                    </div>
                @endif

                @if(count($lenders) > 0)
                <hr/>
                <div class="loan-section">
                    <div class="loan-section-title">
                        <span class="text-light">Lenders</span>
                    </div>
                    <div class="loan-section-content">
                        @include('partials.loan-lenders', compact('lenders'))
                    </div>
                </div>
                @endif

                @if(Auth::check() && Auth::getUser()->isAdmin())
                <br><br>
                <a href="{{ route('admin:loan-feedback', $loan->getId()) }}">Give Feedback</a>
                @endif
            </div>

            <div class="tab-pane fade" id="discussion">
                
                <div class="loan-section">

                    <div class="loan-section-title">
                        <span class="text-light">Discussion</span>
                    </div>
                    
                    <div class="loan-section-content">
                        <span class="text-light">
                            Ask {{ $borrower->getFirstName() }} a question about this loan project, share news and photos of your own, or send a simple note of thanks or inspiration.
                            <br/><br/>
                        </span>
                    </div>
                </div>
                
                @include('partials.comments.comments', [
                    'comments' => $comments,
                    'receiver' => $borrower,
                    'controller' => 'BorrowerCommentController',
                    'canPostComment' => true,
                    'canReplyComment' => true
                ])
            </div>

            <div class="tab-pane fade" id="repayment">
                @if($loan->isDisbursed())
                <div>
                    @include('partials.repayment-schedule-table', ['repaymentSchedule' => $repaymentSchedule, 'dollarExchangeRate' => $disbursedExchangeRate])
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-sm-4 loan-side" style="padding-left:0;">
        <div class="panel panel-default panel-body {{ $loan->isOpen() ? 'sidenav bid-form' : '' }}">
            @if(strlen($loan->getSummary()) <= 60)
                <h2>{{ $loan->getSummary() }}</h2>
            @else
                <h3>{{ $loan->getSummary() }}</h3>
            @endif
            
            <p class="text-light">
                <i class="fa fa-fw fa-user"></i>
                {{ $borrower->getName() }}
                <br/>
                <i class="fa fa-fw fa-map-marker"></i>
                {{ $borrower->getProfile()->getCity() }},
                {{ $borrower->getCountry()->getName() }}
            </p>
            
            @if($loan->isFunded())
            <span class="label label-info">
                Pending disbursement
            </span>
            @endif

            @if($loan->isExpired())
            <span class="label label-default">
                Loan expired
            </span>
            @include('partials/loan-progress', [ 'loan' => $loan ])
            @endif

            @if($loan->isCanceled())
            <span class="label label-default">
                The loan was canceled
            </span>
            @endif

            @if($loan->isDefaulted())
            <span class="label label-default">
                Loan defaulted
            </span>
            <br/>
            <br/>
            @endif

            @if($loan->isDisbursed())
                @include('loan.partials.repaid-bar', compact('loan'))
            @endif

            @if($loan->isOpen())
                @if(Auth::check() && Auth::user()->isBorrower())
                    @include('partials/loan-progress', [ 'loan' => $loan ])
                @else
                    @include('loan.partials.lend-form')
                @endif
            @endif
        </div>


        <div class="panel-body">
        @if(Auth::check())
            @if(Auth::user()->isLender())
            <a
                id="follow-button"
                href="{{ route('lender:follow', $borrower->getId()) }}"
                class="btn btn-default btn-block followBorrower"
                style="{{ $follower ? 'display:none' : '' }}"
                data-follow="follow"
                data-toggle="tooltip">
                
                <i class="fa fa-fw fa-bookmark"></i>
                Follow {{ $borrower->getFirstName() }}
            </a>
            @include('lender.follow.follower', [
                'lender' => Auth::user()->getLender(),
                'follower' => $follower,
            ])
            @endif
        @else
            <div class="text-center">
                @lang('lender.follow.login', ['name' => $borrower->getFirstName(), 'link' => route('login')])                        
            </div>
        @endif
        </div>

        @if(Auth::check() && Auth::user()->isBorrower() && Auth::id() == $loan->getBorrowerId())
        <div class="panel-body">
            <a class="btn btn-primary btn-block" href="{{ route('borrower:loan', $loan->getId()) }}">
                @lang('borrower.loan.public.loan-page')
            </a>
        </div>
        @endif
        
    </div>
</div>

@if(Auth::check() && Auth::user()->isBorrower() && Auth::id() == $loan->getBorrowerId())
    <a class="btn btn-primary btn-block mobile-bottom-btn" href="{{ route('borrower:loan', $loan->getId()) }}">
        @lang('borrower.loan.public.loan-page')
    </a>
@endif

@if($loan->isOpen() && !(Auth::check() && Auth::user()->isBorrower()))
    <button id="mobile-lend-btn" type="button" class="btn btn-primary btn-block mobile-bottom-btn">Lend</button>
@endif
@stop

@section('script-footer')
<script type="text/javascript">
    $(function() {
        var hash = document.location.hash;
        if (hash.substring(1, 8) == 'comment') {
            $('.nav-tabs a[href=#discussion]').tab('show');
        }

        $('.nav-tabs a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });

        $('.followBorrower').tooltip({placement: 'bottom', title: 'Receive an email when this borrower posts a new comment or loan application.'})
    });
</script>
@append
