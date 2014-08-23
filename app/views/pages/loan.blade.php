@extends('layouts.master')

@section('page-title')
    {{ $loan->getBorrower()->getName() }}
@stop

@section('content-top')
    <div class="container page-section loan-titlebar" style="padding-bottom: 5px;">
        <div class="row">
            <div class="col-xs-12">
                <div class="loan-side">
                    <div class="loan-title">
                        <h3>
                            {{ $loan->getSummary() }}
                        </h3>
                    </div>
                    
                    <p class="text-light">
                        <i class="fa fa-fw fa-user"></i>
                        {{ $loan->getBorrower()->getName() }}
                        <br/>
                        <i class="fa fa-fw fa-map-marker"></i>
                        {{ $loan->getBorrower()->getProfile()->getCity() }},
                        {{ $loan->getBorrower()->getCountry()->getName() }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-sm-8 loan-body">
        
        <div class="pull-left profile-image" href="{{ route('loan:index', $loan->getId()) }}"
            style="background-image:url(/assets/images/test-photos/esther.JPG)" width="100%" height="450px">
        </div>
        <!--
        <img src="{{ $loan->getBorrower()->getUser()->getProfilePictureUrl('large-profile-picture') }}" width="100%">
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
                                <strong>{{{ $loan->getBorrower()->getName() }}}</strong>
                                <br/>
                                <strong>
                                    <a href="https://www.google.com/maps/place/{{ $loan->getBorrower()->getProfile()->getCity() }},+{{ $loan->getBorrower()->getCountry()->getName() }}/">{{ $loan->getBorrower()->getProfile()->getCity() }}</a>,
                                    {{ $loan->getBorrower()->getCountry()->getName() }}
                                </strong>
                                <br/>
                                Invited By:
                                <a href="#">TO DO</a>
                                <br/>
                                Volunteer Mentor:
                                <a href="#">TO DO</a>
                            </div>
                            <div class="col-sm-6">
                                Followers: 
                                <strong>@choice('lender.follow.count', $followersCount)</strong>
                                <br/>

                                <a id="follow-link" href="#">Follow {{{ $loan->getBorrower()->getFirstName() }}}</a>

                                Feedback Rating:<i class="fa fa-info-circle rating" data-toggle="tooltip"></i>
                                <strong>TODO</strong>
                                <br/>

                                @if($displayFeedbackComments)
                                    <p><a href="#feedback">View Lender Feedback</a></p>
                                @endif

                                On-Time Repayments:<i class="fa fa-info-circle repayment" data-toggle="tooltip"></i>
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
                                    <strong>${{ $loan->getDisbursedAmount() }}</strong>
                                    <br/>
                                    Date Disbursed: 
                                    <strong>{{ $loan->getDisbursedAt()->format('M j, Y') }}</strong>
                                    <br/>
                                    Repayment period:<i class="fa fa-info-circle repaymentPeriod" data-toggle="tooltip"></i>
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
                                    <strong>TO DO</strong>
                                    <br/>
                                    Application expires:
                                    <strong>TO DO</strong>
                                @endif
                            </div>
                            @if($loan->isDisbursed())
                            <div class="col-sm-6">     
                                Lender interest:</b><i class="fa fa-info-circle totalInterest" data-toggle="tooltip"></i> 
                                <strong>${{ $totalInterest }} TO DO</strong> 
                                <br/>
                                Service fee:</b><i class="fa fa-info-circle transactionFee" data-toggle="tooltip"></i> 
                                <strong>${{ $serviceFee->getAmount() }}</strong>
                                <br/>
                                Total cost of loan:<i class="fa fa-info-circle repaidAmount" data-toggle="tooltip"></i>
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

                        <p>{{ $loan->getBorrower()->getProfile()->getAboutMe() }}</p>
                        
                        @if(Auth::check() && Auth::getUser()->isAdmin())
                            <a href="{{ route('admin:get-translate', $loan->getId()) }}#about-me">Edit translation</a>
                        @endif

                        @if($loan->getBorrower()->getProfile()->getAboutMeTranslation())
                        <div>
                            <p class="text-right">
                                <a href="#" data-toggle="collapse" data-target="#toggle-aboutMe" data-toggle-text="Hide original language">
                                    Display posting in original language
                                </a>
                            </p>

                            <div id="toggle-aboutMe" class="collapse">
                                <p>
                                    {{ $loan->getBorrower()->getProfile()->getAboutMeTranslation() }}
                                </p>
                            </div>
                        </div>
                        @endif

                        <h5>My Business</h5>

                        <p>{{ $loan->getBorrower()->getProfile()->getAboutBusiness() }}</p>
                        
                        @if(Auth::check() && Auth::getUser()->isAdmin())
                            <a href="{{ route('admin:get-translate', $loan->getId()) }}#about-business">Edit translation</a>
                        @endif

                        @if($loan->getBorrower()->getProfile()->getAboutBusinessTranslation())
                        <div>
                            <p class="text-right">
                                <a  href="#" data-toggle="collapse" data-target="#toggle-aboutBusiness" data-toggle-text="Hide original language">
                                    Display posting in original language
                                </a>
                            </p>

                            <div id="toggle-aboutBusiness" class="collapse">
                                <p>
                                    {{ $loan->getBorrower()->getProfile()->getAboutBusinessTranslation() }}
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
                            Ask {{ $loan->getBorrower()->getFirstName() }} a question about this loan project, share news and photos of your own, or send a simple note of thanks or inspiration.
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
        <div class="panel panel-default panel-body sidenav">
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
                {{ $loan->getBorrower()->getName() }}
                <br/>
                <i class="fa fa-fw fa-map-marker"></i>
                {{ $loan->getBorrower()->getProfile()->getCity() }},
                {{ $loan->getBorrower()->getCountry()->getName() }}
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

            <!-- TO DO: this button should open the lend form full screen on a mobile device -->
            <button id="mobile-lend-button" type="button" class="btn btn-primary btn-block">Lend</button>

            <div class="lend-form">
                <div id="lend-form-initial">
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
                    
                    @if (!Request::query('amount'))
                        @if (\Auth::check())
                            @if (\Auth::user()->isLender())
                                <button id="lend-action" type="button" class="btn btn-primary btn-block">Lend</button>
                            @endif
                        @else
                            <a href="{{ route('lender:join') }}" id="join-lend" class="btn btn-primary btn-block" data-toggle="modal" data-target="#join-modal">Lend</a>
                        @endif
                    @endif
                </div> <!-- /lend-form-initial -->
                
                <div id="lend-details" class="lend-details" {{ Request::query('amount') ? '' : 'style="display:none;"' }}>
                    {{ BootstrapForm::hidden('creditAmount', null, ['id' => 'credit-amount']) }}
                    
                    {{ BootstrapForm::hidden('donationCreditAmount', null, ['id' => 'donation-credit-amount']) }}
                    {{ BootstrapForm::hidden('loanId', $loan->getId()) }}

                    {{ BootstrapForm::hidden('transactionFee', null, ['id' => 'transaction-fee-amount']) }}
                    {{ BootstrapForm::hidden('transactionFeeRate', null, ['id' => 'transaction-fee-rate']) }}
                    {{ BootstrapForm::hidden('currentBalance', null, ['id' => 'current-balance']) }}
                    {{ BootstrapForm::hidden('isLenderInviteCredit', null, ['id' => 'current-balance']) }}
                    {{ BootstrapForm::hidden('totalAmount', null, ['id' => 'total-amount']) }}

                    {{ BootstrapForm::hidden('stripeToken', null, ['id' => 'stripe-token']) }}
                    {{ BootstrapForm::hidden('paymentMethod', null, ['id' => 'payment-method']) }}

                    <table class="table">
                        <tbody>
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
                                <td>Loan for {{ $loan->getBorrower()->getFirstName() }}</td>
                                <td>$<span id="amount-display"></span></td> 
                            </tr>
                            <tr style="display: none;">
                                <td>
                                    Credit card fee
                                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="Covers credit card charges"></i>
                                </td>
                                <td>$<span id="fee-amount-display"></span></td>
                            </tr>
                            <tr>
                                <td>
                                    Donation to Zidisha
                                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="Helps with our operating costs"></i>
                                </td>
                                <td style="width: 100px;">
                                        {{ BootstrapForm::text('donationAmount', null, [
                                            'id'      => 'donation-amount',
                                            'label'   => false,
                                        ]) }}
                                <!-- TO DO: make the default 15% of the loan amount -->
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Total</strong></td>
                                <td>$<strong><span id="total-amount-display"></span></strong></td>
                            </tr>
                        </tbody>
                    </table>
        
<!--                    <button id="stripe-payment" class="btn btn-primary">Pay With Card</button>-->
<!--                    <input type="submit" id="paypal-payment" class="btn btn-default btn-block" value="Pay with Paypal" name="submit_paypal">-->

                    <input type="submit" id="balance-payment" class="btn btn-primary btn-block" value="Confirm" name="submit_credit">

                    <button type="button" id="stripe-payment" class="btn btn-primary btn-block btn-icon">
                        <span class="icon-container">
                            <span class="fa fa-credit-card fa-lg fa-fw"></span>
                        </span>
                        <span class="text-container">
                             Pay with credit card
                        </span>
                    </button>

                    <button type="submit" id="paypal-payment" class="btn btn-default btn-block">
                        Continue with
                        <img src="http://logocurio.us/wp-content/uploads/2014/04/paypal-logo.png" alt="Paypal" style="height: 28px"/>
                    </button>
                </div>
                {{ BootstrapForm::close() }}
            </div>
        </div>
            @endif
        
        <div class="panel-body">
            <button id="follow-button" type="button" class="btn btn-default btn-block followBorrower" data-toggle="tooltip">
                <i class="fa fa-fw fa-bookmark"></i>
                @lang('lender.follow.title', ['name' => $borrower->getFirstName()])
            </button>
        </div>
        
    </div>
</div>

<div class="modal fade" id="join-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">

        </div>
    </div>
</div>
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
    });
</script>
@endif
<script type="text/javascript">
    $('.repayment').tooltip({placement: 'bottom', title: 'Percentage of all repayment installments that the borrower has paid on time (within ten days of the due date), for all loans that he or she has taken since joining Zidisha. The total number of repayment installments that have been due is displayed in parentheses.'})
</script>
<script type="text/javascript">
    $('.rating').tooltip({placement: 'bottom', title: 'Percentage of positive feedback ratings posted by previous lenders. The total feedback ratings received are in parentheses.'})
</script>
<script type="text/javascript">
    $('.repaymentPeriod').tooltip({placement: 'bottom', title: 'Time from disbursement until loan is fully' +
        ' repaid'})
</script>
<script type="text/javascript">
    $('.totalInterest').tooltip({placement: 'bottom', title: 'Total interest due to lenders'})
</script>
<script type="text/javascript">
    $('.transactionFee').tooltip({placement: 'bottom', title: 'Covers the cost of transferring funds to the borrower'})
</script>
<script type="text/javascript">
    $('.repaidAmount').tooltip({placement: 'bottom', title: 'Total cost to the borrower for this loan (interest + service fee)'})
</script>
<script type="text/javascript">
    $('.followBorrower').tooltip({placement: 'bottom', title: 'Receive an email when this borrower posts a new comment or loan application.'})
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.previous-loans').click(function () {
            $("#toggle-example").collapse('toggle');
            return false;
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.original-aboutMe').click(function () {
            $("#toggle-aboutMe").collapse('toggle');
            return false;
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.original-aboutBusiness').click(function () {
            $("#toggle-aboutBusiness").collapse('toggle');
            return false;
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.original-proposal').click(function () {
            $("#toggle-proposal").collapse('toggle');
            return false;
        });
    });
    
    $(function() {
        $('.fa-info-circle').tooltip();
        $('#lend-action').on('click', function() {
            $('#lend-details').show();
            $('#lend-form-initial').hide();
            return false;
        });
        $('#join-lend').on('click', function() {
            var data = $(this).closest('form').serialize();
            $.post("{{ route('lender:join-lend') }}", data);
        });

        var hash = document.location.hash;
        if (hash.substring(1, 8) == 'comment') {
            $('.nav-tabs a[href=#discussion]').tab('show');
        }
    });
    $('.nav-tabs a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    })
</script>
@stop
