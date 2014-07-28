@extends('layouts.master')

@section('page-title')
    {{ $loan->getBorrower()->getName() }}
@stop

@section('content-top')
    <div class="page-section loan-titlebar">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1>
                        {{{ $loan->getSummary() }}}
                    </h1>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3 col-md-2">
                    <p>
                        {{ $loan->getBorrower()->getName() }}
                    </p>
                </div>
                <div class="col-sm-4 col-md-5">
                    <p>
                        <i class="fa fa-fw fa-map-marker"></i>
                        {{ $loan->getBorrower()->getProfile()->getCity() }},
                        {{ $loan->getBorrower()->getCountry()->getName() }}
                    </p>
                </div>
                <div class="col-sm-5 col-md-5"> <!-- TO DO: add social share scripts -->
                    <p>
                         <img class="social-icons" src="{{ '/assets/images/test-photos/share.png' }}"/>
                    </p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-sm-8 loan-body">
        <img src="{{ $loan->getBorrower()->getUser()->getProfilePictureUrl('large-profile-picture') }}" width="100%">

        <ul class="nav nav-tabs nav-justified" role="tablist">
            <li class="active"><a href="#about" role="tab" data-toggle="tab">About</a></li>
            <li><a href="#discussion" role="tab" data-toggle="tab">Discussion <span class="badge badge-danger">#</span></a></li>
            @if($loan->isActive())
            <li><a href="#repayment" role="tab" data-toggle="tab">Repayment</a></li>
            @endif
        </ul>

        <div id="tab-content" class="tab-content">
            <div class="tab-pane fade active in" id="about">
        
                <div class="loan-section">
                    <div class="loan-section-title">
                        <span class="text-light">Summary</span>
                    </div>
                    <div class="loan-section-content">
                        <p class="omega">
                            {{{ $loan->getSummary() }}}
                        </p>
                    </div>
                </div>
                    <div class="col-sm-6">
                        On-Time Repayments: <a href="#" class="repayment" data-toggle="tooltip">(?)</a>
                        <strong>{{ $repaymentScore }}%</strong> (TODO)
                        <br/>

                        Feedback Rating: <a href="#" class="rating" data-toggle="tooltip">(?)</a>
                        <strong>TODO</strong>
                        <br/>
                    </div>
                </div>

                <hr/>
                
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
                                    {{ $loan->getBorrower()->getProfile()->getCity() }},
                                    {{ $loan->getBorrower()->getCountry()->getName() }}
                                </strong>
                                <br/>
                                Invited By:
                                <strong>Someone</strong>
                            </div>
                            <div class="col-sm-6">
                                On-Time Repayments: <a href="#" class="repayment" data-toggle="tooltip">(?)</a>
                                <strong>TODO</strong>
                                <br/>

                                Feedback Rating: <a href="#" class="rating" data-toggle="tooltip">(?)</a>
                                <strong>TODO</strong>
                                <br/>

                                Followers: 
                                <strong>@choice('lender.follow.count', $followersCount)</strong>
                                <br/>

                                <a id="follow-link" href="#">Follow {{{ $loan->getBorrower()->getFirstName() }}}</a>

                                @if($displayFeedbackComments)
                                    <p><a href="#feedback">View Lender Feedback</a></p>
                                @endif
                                
                                @if($previousLoans != null)
                                <div class="DemoBS2">
                                    <!-- Toogle Buttons -->
                                    <a class="previous-loans" id="toggle-btn"
                                       data-toggle="collapse" data-target="#toggle-example">View Previous Loans</a>

                                    <div id="toggle-example" class="collapse">
                                        @foreach($previousLoans as $oneLoan)
                                        <p><a href="{{ route('loan:index', $oneLoan->getId()) }}">${{ $oneLoan->getNativeAmount() }}
                                                {{ $oneLoan->getAppliedAt()->format('d-m-Y') }}
                                                {{-- TODO $oneLoan->getAcceptedAt()->format('d-m-Y')
                                                $oneLoan->getExpiredDate()->format('d-m-Y')
                                                TODO change nativeAmount to disbursedAmount in USD
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
                        <div class="row">
                            <div class="col-sm-6">
                                Amount:
                                <strong>{{{ $loan->getUsdAmount() }}}</strong>
                                <br/>
                            </div>
                            <div class="col-sm-6">

                            </div>
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
                                <a data-toggle="collapse" data-target="#toggle-aboutMe" data-toggle-text="Hide original language">
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
                                <a data-toggle="collapse" data-target="#toggle-aboutBusiness" data-toggle-text="Hide original language">
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
                                <a data-toggle="collapse" data-target="#toggle-proposal" data-toggle-text="Hide original language">
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


                <div class="loan-section">
                    <div class="loan-section-title">
                        <span class="text-light">Lenders</span>
                    </div>
                </div>

                    
                @foreach($bids as $bid)
                    <div class="loan-section">
                        <div class="media">
                            <div class="loan-section-title">
                                @if($bid->getLender()->getUser()->getProfilePictureUrl())
                                    <a class="pull-left" href="{{ $bid->getLender()->getUser()->getProfileUrl() }}">
                                        <img class="media-object" width="90px" height="90px" src="{{ $bid->getLender()->getUser()->getProfilePictureUrl() }}" alt="">
                                    </a>
                                @else
                                <a class="pull-left">
                                    <img class="media-object" width="90px" height="90px" src="{{ asset('/assets/images/default.jpg') }}" alt="">
                                </a>
                                @endif
                            </div>

                            <div class="media-body loan-section-content">

                                <hr/>
                                
                                <h4 class="media-heading">
                                    <a href="{{ route('lender:public-profile', $bid->getLender()->getUser()->getUserName()) }}">{{
                                    $bid->getLender()->getUser()->getUserName() }}</a>
                                </h4>
                                <p>
                                    City, Country
                                </p>
                                <p>
                                    <a href="#">Lending Group affiliation</a>
                                </p>
                                <p>
                                    Profile "about me" text goes here
                                </p>
                            </div>
                        </div>
                    </div> 
                @endforeach

                @if(Auth::check() && Auth::getUser()->isAdmin())
                <br><br>
                <a href="{{ route('admin:loan-feedback', $loan->getId()) }}">Give Feedback</a>
                @endif
            </div>

            <div class="tab-pane fade" id="discussion">
                
                <div class="loan-section comments">

                    <div class="loan-section-title">
                        <span class="text-light">Discussion</span>
                    </div>
                    
                    <div class="loan-section-content">
                        <span class="text-light">
                            Ask {{ $loan->getBorrower()->getFirstName() }} a question about this loan, inquire about the business, or send a simple note of thanks or inspiration.
                        </span>
                    </div>

                    @include('partials.comments.comments', [
                        'comments' => $comments,
                        'receiver' => $borrower,
                        'controller' => 'BorrowerCommentController',
                        'canPostComment' => \Auth::check(),
                        'canReplyComment' => \Auth::check()
                    ])
                </div> 
            </div>

            <div class="tab-pane fade" id="repayment">
                @if($loan->getStatus() >= Zidisha\Loan\Loan::ACTIVE)
                <div>
                    <strong>REPAYMENT SCHEDULE</strong>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Date Due</th>
                            <th>Amount Due</th>
                            <th>Date Paid</th>
                            <th>Paid Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($repaymentSchedule as $repaymentScheduleInstallment)
                        <tr>
                            <td>{{ $repaymentScheduleInstallment->getInstallment()->getDueDate()->format('d-m-Y') }}</td>
                            <td>{{ $repaymentScheduleInstallment->getInstallment()->getAmount() }}</td>
                            <?php $i = 0; ?>
                            @foreach($repaymentScheduleInstallment->getPayments() as $repaymentScheduleInstallmentPayment)
                            @if($i > 0)
                        <tr>
                            <td></td>
                            <td></td>
                            <td>{{ $repaymentScheduleInstallmentPayment->getPayment()->getPaidDate()->format('d-m-Y') }}</td>
                            <td>{{ $repaymentScheduleInstallmentPayment->getAmount() }}</td>
                        </tr>
                        @else
                        <td>{{ $repaymentScheduleInstallmentPayment->getPayment()->getPaidDate()->format('d-m-Y') }}</td>
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

    <div class="col-sm-4 loan-side">
        @if(Auth::check() && Auth::getUser()->isAdmin())
        <div class="panel panel-default">
            <div class="panel-body">
                {{ BootstrapForm::open(['route' => ['admin:post-category', $loan->getId()]]) }}
                {{ BootstrapForm::populate($categoryForm) }}

                {{ BootstrapForm::select('category', $categoryForm->getCategories(), $loan->getCategoryId()) }}

                {{ BootstrapForm::select('secondaryCategory', $categoryForm->getSecondaryCategories(), $loan->getSecondaryCategoryId())}}

                {{ BootstrapForm::submit('save') }}

                {{ BootstrapForm::close() }}

            </div>
        </div>
        @endif

        @if($loan->isActive())
        <div class="panel panel-default">
            <div class="panel-heading"><b>About this Loan</b></div>
            <div class="panel-body">
                <p><b>Loan Principal Disbursed: </b>${{ $loan->getDisbursedAmount() }}</p>

                <p><b>Date Disbursed: </b> {{ $loan->getDisbursedAt()->format('d-m-Y') }}</p>

                <p><b>Repayment period: </b> <a href="#" class="repaymentPeriod" data-toggle="tooltip">(?)</a>
                    {{ $loan->getInstallmentCount() }}
                    @if($loan->getInstallmentPeriod() == 0)
                    months
                    @else
                    weeks
                    @endif
                    //TODO check installment period
                </p>

                <p><b>Total Interest Due to Lenders: </b> <a href="#" class="totalInterest" data-toggle="tooltip">(?)</a>USD
                    {{ $totalInterest }} ({{ $loan->getInterestRate() }}%)</p>

                <p><b>Borrower Transaction Fees: </b> <a href="#" class="transactionFee" data-toggle="tooltip">(?)</a>USD
                                        {{ $serviceFee->getAmount() }} (5.00%)</p>

                <p><b>Total Amount (Including Interest and Transaction Fee) to be Repaid: </b> 
                    <a href="#" class="repaidAmount"
                                                                                                  data-toggle="tooltip">(?)</a>
                    ${{ $totalInterest->add($serviceFee)->getAmount() }} ({{5.00 + $loan->getInterestRate() }}%)
                </p>
            </div>
        </div>
        @endif

        @if($loan->isOpen())
        <div class="panel panel-default lend-form">
            <div class="panel-body">
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
                <br/>
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
                
                <div id="lend-details" class="lend-details" {{ Request::query('amount') ? '' : 'style="display:none;"' }}>
                    {{ BootstrapForm::hidden('creditAmount', null, ['id' => 'credit-amount']) }}
                    
                    {{ BootstrapForm::hidden('donationCreditAmount', null, ['id' => 'donation-credit-amount']) }}
                    {{ BootstrapForm::hidden('loanId', $loan->getId()) }}

                    {{ BootstrapForm::hidden('transactionFee', null, ['id' => 'transaction-fee-amount']) }}
                    {{ BootstrapForm::hidden('transactionFeeRate', null, ['id' => 'transaction-fee-rate']) }}
                    {{ BootstrapForm::hidden('currentBalance', null, ['id' => 'current-balance']) }}
                    {{ BootstrapForm::hidden('totalAmount', null, ['id' => 'total-amount']) }}

                    {{ BootstrapForm::hidden('stripeToken', null, ['id' => 'stripe-token']) }}
                    {{ BootstrapForm::hidden('paymentMethod', null, ['id' => 'payment-method']) }}

                    <table class="table">
                        <tbody>
                            @if($placeBidForm->getCurrentBalance()->isPositive())
                            <tr>
                                <td>Current Balance</td>
                                <td>$ {{ $placeBidForm->getCurrentBalance()->getAmount() }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td>
                                    Credit card fee
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="Covers credit card charges"></i>
                                </td>
                                <td>$<span id="fee-amount-display"></span></td>
                            </tr>
                            <tr>
                                <td>
                                    Donation to Zidisha
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="Helps with our operating costs"></i>
                                </td>
                                <td style="width: 100px;">
                                    {{ BootstrapForm::text('donationAmount', null, [
                                        'id'      => 'donation-amount',
                                        'label'   => false,
                                        'prepend' => '$',
                                    ]) }}
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
        
        <div class="panel-body follow">
            <button type="button" class="btn btn-default btn-block followBorrower" data-toggle="tooltip">
                <i class="fa fa-fw fa-star-o"></i>
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
<script type="text/javascript">
    $(function () {
        paymentForm({
            stripeToken: "{{ \Zidisha\Admin\Setting::get('stripe.publicKey') }}",
            email: "{{ \Auth::check() ? \Auth::user()->getEmail() : '' }}",
            amount: $('#amount')
        });
    });
</script>
<script type="text/javascript">
    $('.repayment').tooltip({placement: 'bottom', title: 'The On-Time Repayment Rate is the percentage of all monthly or weekly loan repayment installments that this member has paid on time (within ten days of the due date), for all loans that he or she has taken since joining Zidisha. The number displayed in parentheses is the total number of monthly or weekly installments that have been due, over which the On-Time Repayment Rate is measured.'})
</script>
<script type="text/javascript">
    $('.rating').tooltip({placement: 'bottom', title: 'The Feedback Rating is based on performance ratings assigned to the ' +
        'borrower’s previous loans by Zidisha lenders. The Feedback Rating score is the percentage of all performance ratings ' +
        'that are positive, and the number displayed in parentheses is the total number of performance ratings the borrower has earned.'})
</script>
<script type="text/javascript">
    $('.repaymentPeriod').tooltip({placement: 'bottom', title: 'Number of months or weeks from disbursement until loan is fully' +
        ' repaid'})
</script>
<script type="text/javascript">
    $('.totalInterest').tooltip({placement: 'bottom', title: 'This is the annual interest rate the borrower will pay for the ' +
        'amount that has been funded by lenders. Lenders may bid to finance the loan at their preferred interest rate. If more ' +
        'bids are received than the amount needed to fund the loan, the borrower will accept the bids with the lowest proposed interest rates.' +

        'All interest rates displayed on the Zidisha website are expressed as flat percentages of loan principal per year the ' +
        'loan is held. For example, for a loan of $100, taken at 4% annual interest with a repayment period of six months, ' +
        'the total interest amount will be USD 100 * 4% * (6 months / 12 months) = $2.' +

        'The expression of interest rates as flat percentages of loan principal amounts is intended to make calculation of ' +
        'interest amounts more intuitive for borrowers and for lenders, and to facilitate comparison with other microfinance ' +
        'loans in borrowers\' communities, the majority of which also use the flat rate methodology to express interest rates.'})
</script>
<script type="text/javascript">
    $('.transactionFee').tooltip({placement: 'bottom', title: 'A transaction fee paid to Zidisha, expressed as a total amount and as a flat annualized percentage of the loan principal amount.'})
</script>
<script type="text/javascript">
    $('.repaidAmount').tooltip({placement: 'bottom', title: 'Interest plus transaction fees, expressed as a total amount and as a flat annualized percentage of the loan principal amount.'})
</script>
<script type="text/javascript">
    $('.followBorrower').tooltip({placement: 'bottom', title: 'Receive an email when this borrower posts a new comment or loan application.'})
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.previous-loans').click(function () {
            $("#toggle-example").collapse('toggle');
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.original-aboutMe').click(function () {
            $("#toggle-aboutMe").collapse('toggle');
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.original-aboutBusiness').click(function () {
            $("#toggle-aboutBusiness").collapse('toggle');
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.original-proposal').click(function () {
            $("#toggle-proposal").collapse('toggle');
        });
    });
    
    $(function() {
        $('.fa-question-circle').tooltip();
        $('#lend-action').on('click', function() {
            $('#lend-details').show();
            $(this).hide();
            return false;
        });
        $('#join-lend').on('click', function() {
            var data = $(this).closest('form').serialize();
            $.post("{{ route('lender:join-lend') }}", data);
        });
    });
    $('.nav-tabs a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    })
</script>
@stop
