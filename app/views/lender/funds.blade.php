@extends('layouts.master')

@section('page-title')
Funds
@stop

@section('content')
<div class="page-header">
    <h1>Add or Withdraw Funds</h1>
</div>


<div>
    <strong>Balance available: USD {{ $currentBalance }} </strong>
</div>

<div>
    <strong>Add Funds</strong>
</div>

{{ BootstrapForm::open(array('route' => 'lender:post-funds', 'translationDomain' => 'fund', 'id' => 'funds-upload')) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::text('creditAmount', null, ['id' => 'credit-amount']) }}
{{ BootstrapForm::text('donationAmount', null, ['id' => 'donation-amount']) }}

{{ BootstrapForm::hidden('transactionFee', null, ['id' => 'transaction-fee-amount']) }}
{{ BootstrapForm::hidden('transactionFeeRate', null, ['id' => 'fee-amount-rate']) }}
{{ BootstrapForm::hidden('currentBalance', 0, ['id' => 'fee-amount-rate']) }}
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

<div>
    <strong>Redeem Gift Card</strong>
</div>
<a href="{{ route('lender:gift-cards') }}">Purchase Gift Card</a>
<br/>
{{ BootstrapForm::open(array('route' => 'lender:post-redeem-card', 'translationDomain' => 'redeemCard')) }}

{{ BootstrapForm::text('redemptionCode') }}

<br/>
<button id="stripe-payment" class="btn btn-primary">Submit</button>

{{ BootstrapForm::close() }}

@stop

@section('script-footer')
<script src="https://checkout.stripe.com/checkout.js"></script>
<script type="text/javascript">
    $(function() {
        paymentForm({
            stripeToken: "{{ \Config::get('stripe.public_key') }}",
            email: "{{ \Auth::user()->getEmail() }}",
            amount: $('credit-amount')
        })
    });
</script>
@stop
