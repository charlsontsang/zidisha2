@extends('layouts.master')

@section('page-title')
@lang('loan.page-title')
@stop

@section('content')
<div class="page-header">
    <h1>{{ $loan->getSummary()}}</h1>
</div>

<div class="row">
    <div class="col-xs-8">
        <h3>My Story</h3>

        <p>{{ $loan->getBorrower()->getProfile()->getAboutMe() }}</p>
        @if(Auth::check() && Auth::getUser()->isAdmin())
        <a href="{{ route('admin:get-translate', $loan->getId()) }}#about-me">Edit translation</a>
        @endif

        @if($loan->getBorrower()->getProfile()->getAboutMeTranslation())
        <div>
            <a class="original-aboutMe" id="toggle-btn"
               data-toggle="collapse" data-target="#toggle-aboutMe">Display posting in original Language</a>

            <div id="toggle-aboutMe" class="collapse">

                <p>
                    {{ $loan->getBorrower()->getProfile()->getAboutMeTranslation() }}
                </p>

            </div>
        </div>
        @endif

        <h3>About My Business</h3>

        <p>{{ $loan->getBorrower()->getProfile()->getAboutBusiness() }}</p>
        @if(Auth::check() && Auth::getUser()->isAdmin())
        <a href="{{ route('admin:get-translate', $loan->getId()) }}#about-business">Edit translation</a>
        @endif

        @if($loan->getBorrower()->getProfile()->getAboutBusinessTranslation())
        <div>
            <a class="original-aboutBusiness" id="toggle-btn"
               data-toggle="collapse" data-target="#toggle-aboutBusiness">Display posting in original Language</a>

            <div id="toggle-aboutBusiness" class="collapse">

                <p>
                    {{ $loan->getBorrower()->getProfile()->getAboutBusinessTranslation() }}
                </p>

            </div>
        </div>
        @endif

        <h3>My Loan Proposal</h3>

        <p>{{ $loan->getProposal() }}</p>
        @if(Auth::check() && Auth::getUser()->isAdmin())
        <a href="{{ route('admin:get-translate', $loan->getId()) }}#proposal">Edit translation</a>
        @endif

        @if($loan->getProposalTranslation())
        <div>
            <a class="original-proposal" id="toggle-btn"
               data-toggle="collapse" data-target="#toggle-proposal">Display posting in original Language</a>

            <div id="toggle-proposal" class="collapse">

                <p>
                    {{ $loan->getProposalTranslation() }}
                </p>

            </div>
        </div>
        @endif
        <br/>
        <br/>
        <h4>Comments</h4>
        @include('partials.comments.comments', ['comments' => $comments])
    </div>

    <div class="col-xs-4">
        @if(Auth::check() && Auth::getUser()->isAdmin())
        <br><br>
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
        <img src="{{ $loan->getBorrower()->getUser()->getProfilePictureUrl() }}" width="300" height="300">

        <h2>{{ $loan->getBorrower()->getFirstName() }} {{ $loan->getBorrower()->getLastName() }}</h2>
        <h4>{{ $loan->getBorrower()->getCountry()->getName() }}</h4>
        <strong>Amount Requested: </strong> USD {{ $loan->getAmount() }}

        <div class="panel panel-default">
            <div class="panel-heading"><b>About {{ $borrower->getName() }}</b></div>
            <div class="panel-body">
                <p><b>On-Time Repayments:</b>
                    <a href="#" class="repayment" data-toggle="tooltip">(?)</a>
                    //TODO
                </p>

                @if($previousLoans != null)
                <div class="DemoBS2">
                    <!-- Toogle Buttons -->
                    <a class="previous-loans" id="toggle-btn"
                       data-toggle="collapse" data-target="#toggle-example">View Previous Loans</a>

                    <div id="toggle-example" class="collapse">
                        @foreach($previousLoans as $oneLoan)
                        <p><a href="{{ route('loan:index', $oneLoan->getId()) }}">USD {{ $oneLoan->getNativeAmount() }}
                                {{ $oneLoan->getApplicationDate()->format('d-m-Y') }}
                                {{-- TODO $oneLoan->getAcceptedDate()->format('d-m-Y')
                                $oneLoan->getExpiredDate()->format('d-m-Y')
                                TODO change nativeAmount to disbursedAmount in USD
                                --}}
                            </a>
                        </p>
                        @endforeach
                    </div>
                </div>
                @endif

                <p><b>Feedback Rating: </b> <a href="#" class="rating" data-toggle="tooltip">(?)</a>
                    //TODO
                </p>

                <p><a href="#">View Lender Feedback</a></p>
                <!-- Generated markup by the plugin -->

            </div>
        </div>

        @if($loan->isActive())
        <div class="panel panel-default">
            <div class="panel-heading"><b>About this Loan</b></div>
            <div class="panel-body">
                <p><b>Loan Principal Disbursed: </b>USD {{ $loan->getNativeDisbursedAmount() }}</p>

                <p><b>Date Disbursed: </b> {{ $loan->getDisbursedDate()->format('d-m-Y') }}</p>

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
                    {{ $transactionFee }} (5.00%)</p>

                <p><b>Total Amount (Including Interest and Transaction Fee) to be Repaid: </b> <a href="#" class="repaidAmount"
                                                                                                  data-toggle="tooltip">(?)</a>
                    USD {{ $totalInterest+$transactionFee }} ({{5.00 + $loan->getInterestRate() }}%)
                </p>
            </div>
        </div>
        @endif

        @include('partials/_progress', [ 'raised' => $raised])

        @if($loan->isOpen())
        <div>
            {{ BootstrapForm::open(array('route' => ['loan:place-bid', $loan->getId()], 'translationDomain' => 'bid', 'id' => 'funds-upload')) }}
            {{ BootstrapForm::populate($form) }}

            {{ BootstrapForm::text('amount', null, ['id' => 'amount']) }}
            {{ BootstrapForm::hidden('creditAmount', null, ['id' => 'credit-amount']) }}
            {{ BootstrapForm::text('donationAmount', null, ['id' => 'donation-amount']) }}

            {{ BootstrapForm::select('interestRate', $form->getRates()) }}
            {{ BootstrapForm::hidden('loanId', $loan->getId()) }}

            {{ BootstrapForm::hidden('transactionFee', null, ['id' => 'transaction-fee-amount']) }}
            {{ BootstrapForm::hidden('transactionFeeRate', null, ['id' => 'fee-amount-rate']) }}
            {{ BootstrapForm::hidden('currentBalance', null, ['id' => 'current-balance']) }}
            {{ BootstrapForm::hidden('totalAmount', null, ['id' => 'total-amount']) }}

            {{ BootstrapForm::hidden('stripeToken', null, ['id' => 'stripe-token']) }}
            {{ BootstrapForm::hidden('paymentMethod', null, ['id' => 'payment-method']) }}

            @if($form->getCurrentBalance()->isPositive())
            {{ BootstrapForm::label("Current Balance") }}: {{ $form->getCurrentBalance() }}
            <br/>
            @endif

            {{ BootstrapForm::label("Payment Transfer Cost") }}:
            USD <span id="fee-amount-display"></span>

            <br/>

            {{ BootstrapForm::label("Total amount to be charged to your account") }}
            USD <span id="total-amount-display"></span>

            <br/>

            <button id="stripe-payment" class="btn btn-primary">Pay With Card</button>
            <input type="submit" id="paypal-payment" class="btn btn-primary" value="Pay With Paypal" name="submit_paypal">
            <input type="submit" id="credit-payment" class="btn btn-primary" value="Pay" name="submit_credit">

            {{ BootstrapForm::close() }}

        </div>
        @endif

        <br>
        <strong>FUNDING RAISED </strong>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Date</th>
                <th>Lender</th>
                <th>Amount (USD)</th>
            </tr>
            </thead>
            <tbody>
            @foreach($bids as $bid)
            <tr>
                <td>{{ $bid->getBidDate()->format('d-m-Y') }}</td>
                <td><a href="{{ route('lender:public-profile', $bid->getLender()->getUser()->getUserName()) }}">{{
                        $bid->getLender()->getUser()->getUserName() }}</a></td>
                <td>{{ $bid->getBidAmount() }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        <strong>Raised: </strong> USD {{ $totalRaised }}
        <strong>Still Needed: </strong> USD {{ $stillNeeded }}

        @if(Auth::check() && Auth::getUser()->isAdmin())
        <br><br>
        <a href="{{ route('admin:loan-feedback', $loan->getId()) }}">Give Feedback</a>
        @endif
    </div>
</div>
@stop

@section('script-footer')
<script src="https://checkout.stripe.com/checkout.js"></script>
<script type="text/javascript">
    $(function () {
        paymentForm({
            stripeToken: "{{ \Config::get('stripe.public_key') }}",
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
        'loan is held. For example, for a loan of USD 100, taken at 4% annual interest with a repayment period of six months, ' +
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
</script>
@stop
