@extends('layouts.master')

@section('page-title')
Transfer Funds
@stop

@section('content')
<div class="row lender-funds-page">
    <div class="col-sm-3 col-md-4">
        <ul class="nav side-menu" role="complementary">
          <h4>Quick Links</h4>
            @include('partials.nav-links.lender-links')       
          </ul>
    </div>

    <div class="col-sm-9 col-md-8 info-page">
        <div class="page-header">
            <h1>Transfer Funds</h1>
        </div>

        <p>Current lending credit: <strong>{{ $currentBalance }}</strong></p>
        <br/>

        <h2>Add Funds</h2>

        <p>Transfer funds to your lending account.</p>
        <br/>

        {{ BootstrapForm::open(array('route' => 'lender:post-funds', 'translationDomain' => 'fund', 'id' => 'funds-upload')) }}
        {{ BootstrapForm::populate($form) }}

        {{ BootstrapForm::text('amount', null, ['id' => 'amount']) }}
        {{ BootstrapForm::hidden('creditAmount', null, ['id' => 'credit-amount']) }}
        {{ BootstrapForm::text('donationAmount', null, ['id' => 'donation-amount']) }}
        {{ BootstrapForm::hidden('donationCreditAmount', null, ['id' => 'donation-credit-amount']) }}

        {{ BootstrapForm::hidden('transactionFee', null, ['id' => 'transaction-fee-amount']) }}
        {{ BootstrapForm::hidden('transactionFeeRate', null, ['id' => 'transaction-fee-rate']) }}
        {{ BootstrapForm::hidden('currentBalance', 0, ['id' => 'current-balance']) }}
        {{ BootstrapForm::hidden('totalAmount', null, ['id' => 'total-amount']) }}

        {{ BootstrapForm::hidden('stripeToken', null, ['id' => 'stripe-token']) }}
        {{ BootstrapForm::hidden('paymentMethod', null, ['id' => 'payment-method']) }}

        {{ BootstrapForm::label("Payment Transfer Cost") }}:
        USD <span id="fee-amount-display"></span>

        <br/><br/>

        {{ BootstrapForm::label("Total amount to be charged to your account") }}
        USD <span id="total-amount-display"></span>

        <br/><br/>

        <button id="stripe-payment" class="btn btn-primary">Pay with credit card</button>

        <button type="submit" id="paypal-payment" class="btn btn-default" value="Pay With Paypal" name="submit_paypal">
            Continue with
            <img src="http://logocurio.us/wp-content/uploads/2014/04/paypal-logo.png" alt="Paypal" style="height: 20px"/>
        </button>

        {{ BootstrapForm::close() }}

        <hr/>
        
        <h2>Redeem Gift Card</h2>

        <p>Redeem a gift card you have received.</p>
        <br/>
        <a href="{{ route('lender:gift-cards') }}">Give a gift card</a>
        <br/><br/>

        {{ BootstrapForm::open(array('route' => 'lender:post-redeem-card', 'translationDomain' => 'redeemCard')) }}

        {{ BootstrapForm::text('redemptionCode') }}

        <button id="stripe-payment" class="btn btn-primary">Redeem</button>

        {{ BootstrapForm::close() }}
        
        <hr/>

        <h2>Withdraw Funds</h2>

        <p>Request a transfer of your lending credit to your PayPal account.</p>
        <br/>
        <div>
            <p>Current lending credit: <strong>{{ $currentBalance }}</strong></p>
        </div>
        <br/>

        {{ BootstrapForm::open(array('route' => 'lender:post-withdraw-funds', 'translationDomain' => 'withdrawFunds')) }}

        {{ BootstrapForm::text('paypalEmail') }}
        {{ BootstrapForm::text('withdrawAmount') }}

        <button id="stripe-payment" class="btn btn-primary">Withdraw</button>

        {{ BootstrapForm::close() }}
    </div>
</div>
@stop

@section('script-footer')
<script src="https://checkout.stripe.com/checkout.js"></script>
<script type="text/javascript">
    $(function() {
        paymentForm({
            stripeToken: "{{ \Zidisha\Admin\Setting::get('stripe.publicKey') }}",
            email: "{{ \Auth::check() ? \Auth::user()->getEmail() : '' }}"
        })
    });
</script>
@stop
