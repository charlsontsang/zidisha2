@extends('layouts.master')

@section('page-title')
Add or Withdraw Funds
@stop

@section('content')
<div class="page-header">
    <h1>Add or Withdraw Funds</h1>
</div>


<div>
    <strong>Balance available: ${{ $currentBalance }}</strong>
</div>

<div>
    <strong>Add Funds</strong>
</div>

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

<br/>

{{ BootstrapForm::label("Total amount to be charged to your account") }}
USD <span id="total-amount-display"></span>

<br/>
<button id="stripe-payment" class="btn btn-primary">Pay With Card</button>
<input type="submit" id="paypal-payment" class="btn btn-primary" value="Pay With Paypal" name="submit_paypal">

{{ BootstrapForm::close() }}
<br><br>
<div>
    <strong>Redeem Gift Card</strong>
</div>
<a href="{{ route('lender:gift-cards') }}">Purchase Gift Card</a>
<br/>
{{ BootstrapForm::open(array('route' => 'lender:post-redeem-card', 'translationDomain' => 'redeemCard')) }}

{{ BootstrapForm::text('redemptionCode') }}

<button id="stripe-payment" class="btn btn-primary">Submit</button>

{{ BootstrapForm::close() }}
<br><br>
<div>
    <strong>Withdraw Funds</strong>
</div>
<br>
<p>Use this form to request a transfer of your lending balance to your PayPal account.</p>
<br/>

<div>
    <p>Balance available: ${{ $currentBalance }}</p>
</div>
{{ BootstrapForm::open(array('route' => 'lender:post-withdraw-funds', 'translationDomain' => 'withdrawFunds')) }}

{{ BootstrapForm::text('paypalEmail') }}
{{ BootstrapForm::text('withdrawAmount') }}

<button id="stripe-payment" class="btn btn-primary">Withdraw</button>

{{ BootstrapForm::close() }}
<br><br>

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
