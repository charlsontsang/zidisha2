@extends('layouts.side-menu')

@section('page-title')
Transfer Funds
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.lender-links')
@stop

@section('page-content')
<p>Current lending credit: <strong>{{ $currentBalance }}</strong></p>

<br/>

<h4>Add Funds</h4>

<p>Transfer funds to your lending account.</p>
<br/>

{{ BootstrapForm::open(array('route' => 'lender:post-funds', 'translationDomain' => 'fund', 'id' => 'funds-upload')) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::text('amount', null, ['label' => 'Lending Credit']) }}
{{ BootstrapForm::hidden('creditAmount', null, ['id' => 'credit-amount']) }}
{{ BootstrapForm::text('donationAmount', null, ['label' => 'Donation to Zidisha']) }}
{{ BootstrapForm::hidden('donationCreditAmount', null, ['id' => 'donation-credit-amount']) }}

{{ BootstrapForm::hidden('transactionFee', null, ['id' => 'transaction-fee-amount']) }}
{{ BootstrapForm::hidden('transactionFeeRate', null, ['id' => 'transaction-fee-rate']) }}
{{ BootstrapForm::hidden('currentBalance', 0, ['id' => 'current-balance']) }}
{{ BootstrapForm::hidden('totalAmount', null, ['id' => 'total-amount']) }}

{{ BootstrapForm::hidden('stripeToken', null, ['id' => 'stripe-token']) }}
{{ BootstrapForm::hidden('paymentMethod', null, ['id' => 'payment-method']) }}

<p>
    Payment Transfer Cost: $<span id="fee-amount-display"></span>
</p>

<p>
    Total Payment: $<span id="total-amount-display"></span>
</p>

<div class="lend-form">
    @include('partials/payment-buttons')
</div>

{{ BootstrapForm::close() }}

<br/><br/>

<h4>Redeem Gift Card</h4>

<p>
    Redeem a gift card you have received.&nbsp;&nbsp;&nbsp;<a href="{{ route('lender:gift-cards') }}">Give a gift card</a>
</p>

{{ BootstrapForm::open(array('route' => 'lender:post-redeem-card')) }}

{{ BootstrapForm::text('redemptionCode', null, ['label' => 'Enter Redemption Code']) }}

<button id="stripe-payment" class="btn btn-primary">Redeem</button>

{{ BootstrapForm::close() }}

<br/><br/>

<h4>Withdraw Funds</h4>

<p>
    Request a transfer of your lending credit to your PayPal account.
</p>

<p>
    Current lending credit: <strong>{{ $currentBalance }}</strong>
</p>

{{ BootstrapForm::open(array('route' => 'lender:post-withdraw-funds')) }}

{{ BootstrapForm::text('paypalEmail', null, ['label' => 'Your PayPal Account Address']) }}
{{ BootstrapForm::text('withdrawAmount', null, ['label' => 'Amount to Withdraw']) }}

<button id="stripe-payment" class="btn btn-primary">Submit</button>

{{ BootstrapForm::close() }}
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
