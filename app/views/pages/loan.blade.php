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
</div> <!-- /container -->
<div class="container-fluid lend" style="padding-top: 0 !important;">
    <div class="container">
        <div class="row">
            <div class="col-sm-8 loan-body">
                
                <div class="pull-left profile-image" href="{{ route('loan:index', $loan->getId()) }}"
                    style="background-image:url(/assets/images/test-photos/esther.JPG)" width="100%" height="450px">
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
                    @if($loan->isActive())
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
                                            <a href="https://www.google.com/maps/place/{{ $borrower->getProfile()->getCity() }},+{{ $borrower->getCountry()->getName() }}/" target="_blank">{{ $borrower->getProfile()->getCity() }}</a>
                                            {{ $borrower->getCountry()->getName() }}
                                        </strong>
                                        
                                        @if($invitedBy)
                                        <br/>
                                        Invited By:
                                        <a href="{{ route('borrower:public-profile', $invitedBy->getId()) }}">{{ $invitedBy->getName() }}</a>
                                        @endif
                                        
                                        @if($volunteerMentor)
                                        <br/>
                                        Volunteer Mentor:
                                        <a href="{{ route('borrower:public-profile', $volunteerMentor->getId()) }}">{{ $volunteerMentor->getName() }}</a>
                                        @endif
                                    </div>
                                    <div class="col-sm-6">
                                        Followers: 
                                        <strong>@choice('lender.follow.count', $followersCount)</strong>
                                        <br/>

                                        <div id="follow-link">
                                            <a
                                                href="{{ route('lender:follow', $borrower->getId()) }}"
                                                class="followBorrower"
                                                style="{{ $follower ? 'display:none' : '' }}"
                                                data-follow="follow"
                                                data-toggle="tooltip">
                                                @lang('lender.follow.title', ['name' => $borrower->getFirstName()])
                                            </a>
                                            @if(Auth::check() && Auth::user()->isLender())
                                                @include('lender.follow.follower', [
                                                'lender' => Auth::user()->getLender(),
                                                'follower' => $follower,
                                                ])
                                            @endif
                                        </div>

                                        Feedback Rating:{{ BootstrapHtml::tooltip('borrower.tooltips.loan.feedback-rating') }}
                                        <strong>{{ $feedbackRating }} % Positive ({{ $totalFeedback }})</strong>
                                        <br/>

                                        @if($displayFeedbackComments)
                                            <p><a href="#feedback">View Lender Feedback</a></p>
                                        @endif

                                        On-Time Repayments:{{ BootstrapHtml::tooltip('borrower.tooltips.loan.on-time-repayments') }}
                                        <strong>TODO</strong>
                                        <br/>
                                        
                                        @if($previousLoans != null)
                                        <div class="DemoBS2">
                                            <!-- Toogle Buttons -->
                                            <a href="#" class="previous-loans" id="toggle-btn"
                                               data-toggle="collapse" data-target="#toggle-example">View Previous Loans</a>

                                            <div id="toggle-example" class="collapse">
                                                @foreach($previousLoans as $oneLoan)
                                                <p><a href="{{ route('loan:index', $oneLoan->getId()) }}">${{ $oneLoan->getAmount() }}
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
                                @if($loan->getStatus() >= Zidisha\Loan\Loan::ACTIVE)
                                    <div class="row" style="margin-top: 5px; margin-bottom: 30px; !important;">
                                        <div class="col-xs-9">
                                            <div class="progress" style="margin: 0 !important;">
                                                <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="{{ $loan->getRaisedPercentage() }}" aria-valuemin="0"
                                                     aria-valuemax="100"
                                                     style="width: 50%;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-3">
                                            <strong>XX%</strong> Repaid
                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-sm-6">
                                        @if($loan->isDisbursed())
                                            Amount: 
                                            <strong>{{ $loan->getDisbursedAmount() }}</strong>
                                            <br/>
                                            Date Disbursed: 
                                            <strong>{{ $loan->getDisbursedAt()->format('M j, Y') }}</strong>
                                            <br/>
                                            Repayment period:{{ BootstrapHtml::tooltip('borrower.tooltips.loan.repayment-period') }}
                                            <strong>{{ $loan->getPeriod() }}
                                            @if($loan->getInstallmentPeriod() == 0)
                                            months
                                            @else
                                            weeks
                                            @endif
                                            //TODO check installment period
                                            </strong>
                                        @else
                                            Amount requested:
                                            <strong>{{{ $loan->getUsdAmount() }}}</strong>
                                            <br/>
                                            Still needed:
                                            <strong>{{{ $loan->getStillNeededUsdAmount() }}}</strong>
                                            <br/>
                                            Application expires:
                                            <strong>{{{ $loan->getExpiresAt()->format('M j, Y') }}}</strong>
                                        @endif
                                    </div>
                                    @if($loan->isDisbursed())
                                    <div class="col-sm-6">     
                                        Lender interest:{{ BootstrapHtml::tooltip('borrower.tooltips.loan.lender-interest') }}
                                        <strong>${{ $totalInterest }} TO DO</strong>
                                        <br/>
                                        Service fee:{{ BootstrapHtml::tooltip('borrower.tooltips.loan.service-fee') }}
                                        <strong>${{ $serviceFee->getAmount() }}</strong>
                                        <br/>
                                        Total cost of loan:{{ BootstrapHtml::tooltip('borrower.tooltips.loan.total-cost-of-loan') }}
                                        <strong>${{ $totalInterest->add($serviceFee)->getAmount() }}</strong>
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

                        <hr/>
                        
                        @if($displayFeedbackComments)
                            <div id="feedback" class="loan-section comments">

                                <div class="loan-section-title">
                                    <span class="text-light">Feedback</span>
                                </div>
                                
                                <div class="loan-section-content">
                                </div>

                                @include('partials.comments.comments', [
                                    'comments' => $loanFeedbackComments,
                                    'receiver' => $loan,
                                    'controller' => 'LoanFeedbackController',
                                    'canPostComment' => $canPostFeedback,
                                    'canReplyComment' => $canReplyFeedback
                                ])
                            </div> 

                            <hr/>

                        @endif

                        @if(count($bids) > 0)
                        <div class="loan-section">
                            <div class="loan-section-title">
                                <span class="text-light">Lenders</span>
                            </div>
                            <div class="loan-section-content">
                                <div class="row">
                                    @foreach($bids as $bid)
                                        <div class="col-xs-4">
                                            <div class="lender-thumbnail">
                                                <a href="{{ $bid->getLender()->getUser()->getProfileUrl() }}">
                                                    @if($bid->getLender()->getUser()->getProfilePictureUrl())
                                                        <img src="{{ $bid->getLender()->getUser()->getProfilePictureUrl() }}" alt="">
                                                    @else
                                                        <img src="{{ asset('/assets/images/profile-default1.jpg') }}" alt="">
                                                    @endif
                                                </a>
                                                <h3>
                                                    <a href="{{ route('lender:public-profile', $bid->getLender()->getUser()->getUserName()) }}">
                                                        {{ $bid->getLender()->getUser()->getUserName() }}</a>
                                                </h3>
                                                <p>
                                                    @if($bid->getLender()->getProfile()->getCity())
                                                        {{ $bid->getLender()->getProfile()->getCity() }},&nbsp;
                                                    @endif
                                                    {{ $bid->getLender()->getCountry()->getName() }}
                                                </p>
                                            </div>
                                        </div> 
                                    @endforeach
                                </div>
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
                            'canPostComment' => \Auth::check(),
                            'canReplyComment' => \Auth::check()
                        ])
                    </div>

                    <div class="tab-pane fade" id="repayment">
                        @if($loan->getStatus() >= Zidisha\Loan\Loan::ACTIVE)
                        <div>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th colspan="2">Expected Payments</th>
                                    <th colspan="2">Actual Payments</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($repaymentSchedule as $repaymentScheduleInstallment)
                                <tr>
                                    <td>{{ $repaymentScheduleInstallment->getInstallment()->getDueDate()->format('M j, Y') }}</td>
                                    <td>{{ $repaymentScheduleInstallment->getInstallment()->getAmount() }}</td>
                                    <?php $i = 0; ?>
                                    @foreach($repaymentScheduleInstallment->getPayments() as $repaymentScheduleInstallmentPayment)
                                    @if($i > 0)
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>{{ $repaymentScheduleInstallmentPayment->getPayment()->getPaidDate()->format('M j, Y') }}</td>
                                    <td>{{ $repaymentScheduleInstallmentPayment->getAmount() }}</td>
                                </tr>
                                @else
                                <td>{{ $repaymentScheduleInstallmentPayment->getPayment()->getPaidDate()->format('M j, Y') }}</td>
                                <td>{{ $repaymentScheduleInstallmentPayment->getAmount() }}</td>
                                @endif
                                <?php $i++; ?>
                                @endforeach
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-sm-4 loan-side" style="padding-left:0;">
                @if($loan->isOpen())       
                <div class="panel panel-default panel-body sidenav bid-form">
                @else
                <div class="panel panel-default panel-body">
                @endif
                    <div class="loan-title">
                        <{{ $tag }}>
                            {{ $loan->getSummary() }}
                        </{{ $tag }}>
                    </div>
                    
                    <p class="text-light">
                        <i class="fa fa-fw fa-user"></i>
                        {{ $borrower->getName() }}
                        <br/>
                        <i class="fa fa-fw fa-map-marker"></i>
                        {{ $borrower->getProfile()->getCity() }},
                        {{ $borrower->getCountry()->getName() }}
                    </p>

                    @if($loan->isActive())
                    <div class="panel-heading"><b>Since you last visited...</b></div>
                    
                    July 29 &bull; Lucy posted: "And the piglets came!!!!!!! Yesterday the mother pig gave birth to 8 healthy piglets... <a href="#">>>Read more</a>"
                    <br/><br/>
                    July 26 &bull; Lucy repaid $5.00
                    <br/><br/>
                    July 25 &bull; jkurnia posted: "Thanks for the payment, Lucy!"
                    <br/><br/>
                    July 20 &bull; Lucy adjusted the repayment schedule: "School fees were due this week and... <a href="#">>>Read more</a>"
                    <br/><br/>
                    July 16 &bull; Lucy repaid $10.00

                </div>
                    @endif

                    @if($loan->isOpen())

                    <div class="lend-form">
                        <div id="lend-form-initial" style="{{ \Auth::check() && \Auth::user()->isLender() && \Request::query('amount') ? 'display:none' : '' }}">
                            @include('partials/loan-progress', [ 'loan' => $loan ])
                            
                            {{ BootstrapForm::open(array('route' => ['loan:place-bid', $loan->getId()], 'translationDomain' => 'bid', 'id' => 'funds-upload')) }}
                            {{ BootstrapForm::populate($placeBidForm) }}

                            @if (!\Auth::check() || \Auth::user()->isLender())

                            <div id="lend-form-fields" class="row">
                                <div class="col-xs-6" style="padding-right: 5px">
                                    {{ BootstrapForm::text('amount', Request::query('amount'), [
                                        'id' => 'amount',
                                        'label' => false,
                                        'prepend' => '$'
                                    ]) }}
                                    <div class="text-center text-light">
                                        Loan Amount
                                    </div>
                                </div>
                                <div class="col-xs-6" style="padding-left: 5px">
                                    {{ BootstrapForm::select('interestRate', $placeBidForm->getRates(), Request::query('interestRate'), [
                                        'label' => false
                                    ]) }}
                                    <div class="text-center text-light">
                                        Interest
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if (\Auth::check())
                                @if (\Auth::user()->isLender() && !Request::query('amount'))
                                    <button id="lend-action" type="button" class="btn btn-primary btn-block">Lend</button>
                                @endif
                            @else
                                <a href="{{ route('lender:join') }}" id="join-lend" class="btn btn-primary btn-block" data-toggle="modal" data-target="#join-modal">Lend</a>
                            @endif
                        </div> <!-- /lend-form-initial -->
                        
                        <div id="lend-details" class="lend-details" style="{{ \Auth::check() && \Auth::user()->isLender() && \Request::query('amount') ? '' : 'display:none' }}">
                            {{ BootstrapForm::hidden('creditAmount', null, ['id' => 'credit-amount']) }}
                            
                            {{ BootstrapForm::hidden('donationCreditAmount', null, ['id' => 'donation-credit-amount']) }}
                            {{ BootstrapForm::hidden('loanId', $loan->getId()) }}

                            {{ BootstrapForm::hidden('transactionFee', null, ['id' => 'transaction-fee-amount']) }}
                            {{ BootstrapForm::hidden('transactionFeeRate', null, ['id' => 'transaction-fee-rate']) }}
                            {{ BootstrapForm::hidden('currentBalance', null, ['id' => 'current-balance']) }}
                            {{ BootstrapForm::hidden('isLenderInviteCredit', null, ['id' => 'is-lender-invite-credit']) }}
                            {{ BootstrapForm::hidden('totalAmount', null, ['id' => 'total-amount']) }}

                            {{ BootstrapForm::hidden('stripeToken', null, ['id' => 'stripe-token']) }}
                            {{ BootstrapForm::hidden('paymentMethod', null, ['id' => 'payment-method']) }}

                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Loan for {{ $borrower->getFirstName() }}</td>
                                        <td>$<span id="amount-display"></span></td> 
                                    </tr>
                                    <tr>
                                        <td>
                                            Donation to Zidisha
                                            {{ BootstrapHtml::tooltip('borrower.tooltips.loan.donation-to-zidisha') }}
                                        </td>
                                        <td style="width: 100px;">
                                                {{ BootstrapForm::text('donationAmount', null, [
                                                    'id'      => 'donation-amount',
                                                    'label'   => false,
                                                ]) }}
                                        <!-- TO DO: make the default 15% of the loan amount -->
                                        </td>
                                    </tr>
                                    <tr style="display: none;">
                                        <td>
                                            Credit card fee
                                            {{ BootstrapHtml::tooltip('borrower.tooltips.loan.credit-card-fee') }}
                                        </td>
                                        <td>$<span id="fee-amount-display"></span></td>
                                    </tr>
                                    @if($placeBidForm->getLenderInviteCredit()->isPositive())
                                    <tr>
                                        <td>Lender invite credit</td>
                                        <td>${{ number_format($placeBidForm->getLenderInviteCredit()->getAmount(), 2, '.', '') }}</td>
                                    </tr>
                                    @elseif($placeBidForm->getCurrentBalance()->isPositive())
                                    <tr>
                                        <td>Current Balance</td>
                                        <td>${{ number_format($placeBidForm->getCurrentBalance()->getAmount(), 2, '.', '') }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td><strong>Total</strong></td>
                                        <td>$<strong><span id="total-amount-display"></span></strong></td>
                                    </tr>
                                </tbody>
                            </table>

                            @include('partials/payment-buttons')
                            
                            <input type="submit" id="balance-payment" class="btn btn-primary btn-block" value="Confirm" name="submit_credit">


                        </div>
                        {{ BootstrapForm::close() }}
                    </div>
                </div>
                    @endif
                
                <div class="panel-body">
                    <a
                        id="follow-button"
                        href="{{ route('lender:follow', $borrower->getId()) }}"
                        class="btn btn-default btn-block followBorrower"
                        style="{{ $follower ? 'display:none' : '' }}"
                        data-follow="follow"
                        data-toggle="tooltip">
                        
                        <i class="fa fa-fw fa-bookmark"></i>
                        @lang('lender.follow.title', ['name' => $borrower->getFirstName()])
                    </a>
                    @if(Auth::check() && Auth::user()->isLender())
                    @include('lender.follow.follower', [
                        'lender' => Auth::user()->getLender(),
                        'follower' => $follower,
                    ])
                    @endif
                </div>
                
            </div>
        </div>

        <div class="row">
            <button id="mobile-lend-btn" type="button" class="btn btn-primary btn-block">Lend</button>
        </div>
    </div> <!-- /container -->
</div> <!-- /container-fluid -->
@stop

@section('script-footer')
@if($loan->isOpen() && (!\Auth::check() || \Auth::user()->isLender()))
<script type="text/javascript">
    $(function () {
        var $amount = $('#amount');
        paymentForm({
            stripeToken: "{{ \Zidisha\Admin\Setting::get('stripe.publicKey') }}",
            email: "{{ \Auth::check() ? \Auth::user()->getEmail() : '' }}",
            amount: $amount
        });

        $('#amount-display').text(formatMoney(parseMoney($amount.val()), 2));
        $amount.on('change', function() {
            $('#amount-display').text(formatMoney(parseMoney($amount.val()), 2));
        });
        $amount.on('keyup keypress', function(e) {
            if (e.which  == 13) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
@endif
<script type="text/javascript">
    $('.followBorrower').tooltip({placement: 'bottom', title: 'Receive an email when this borrower posts a new comment or loan application.'})
</script>
<script type="text/javascript">
    $(function() {
        $('.fa-info-circle').tooltip();
        $('#lend-action').on('click', function() {
            $('#lend-details').show();
            $('#lend-form-initial').hide();
            return false;
        });
        $('#join-lend').on('click', function() {
            var data = $(this).closest('form').serialize();
            // https://github.com/laravel/framework/issues/4576
            setTimeout(function() {
                $.post("{{ route('lender:join-lend') }}", data);                
            }, 1000);
        });

        var hash = document.location.hash;
        if (hash.substring(1, 8) == 'comment') {
            $('.nav-tabs a[href=#discussion]').tab('show');
        }
    });
    $('.nav-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
    $('#mobile-lend-btn').on('click', function() {
        var $modal = $($('#modal-template').html()),
            $form = $('.bid-form');
        
        $modal.find('.modal-body').append($form);
        $modal.modal();
        
        $modal.on('hidden.bs.modal', function (e) {
            $('.bid-form').append($form);
        })
    });
</script>
<script type="text/html" id="modal-template">
    <div class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
</script>
@stop
