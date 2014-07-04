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

        <h3>About My Business</h3>

        <p>{{ $loan->getBorrower()->getProfile()->getAboutBusiness() }}</p>

        <h3>My Loan Proposal</h3>

        <p>{{ $loan->getProposal() }}</p>
        <br/>
        <br/>
        <h4>Comments</h4>
        @include('partials.comments.comments', ['comments' => $comments])
    </div>

    <div class="col-xs-4">
        <img src="{{ $loan->getBorrower()->getUser()->getProfilePictureUrl() }}" >
        <h2>{{ $loan->getBorrower()->getFirstName() }} {{ $loan->getBorrower()->getLastName() }}</h2>
        <h4>{{ $loan->getBorrower()->getCountry()->getName() }}</h4>
        <strong>Amount Requested: </strong> USD {{ $loan->getAmount() }}

        @include('partials/_progress', [ 'raised' => $raised])

        @if($loan->isOpen())
            <div>
                {{ BootstrapForm::open(array('route' => 'loan:post-bid', 'translationDomain' => 'bid', 'id' => 'funds-upload')) }}
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
    $(function() {
        paymentForm({
            stripeToken: "{{ \Config::get('stripe.public_key') }}",
            email: "{{ \Auth::user()->getEmail() }}",
            amount: $('#amount')
        });
    });
</script>
@stop
