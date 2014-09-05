@extends('layouts.master')

@section('page-title')
Gift Card Terms of Use
@stop

@section('content')

<p>By purchasing a gift card, I agree to the <a target="_blank" href="/terms-of-use#gift-card">Gift Card Terms of Use</a>.</p>

{{ BootstrapForm::open(array('route' => 'lender:gift-cards:post-terms-accept', 'translationDomain' => 'fund',
'id' => 'funds-upload')) }}
{{ BootstrapForm::populate($paymentForm) }}

{{ BootstrapForm::label("Total Gift Card Amount") }}:
USD {{ $amount }}

<br/>
{{ BootstrapForm::hidden('amount', $amount, ['id' => 'amount']) }}
{{ BootstrapForm::text('donationAmount', null, ['id' => 'donation-amount']) }}
{{ BootstrapForm::hidden('creditAmount', null, ['id' => 'credit-amount']) }}
{{ BootstrapForm::hidden('donationCreditAmount', null, ['id' => 'donation-credit-amount']) }}

{{ BootstrapForm::hidden('transactionFee', null, ['id' => 'transaction-fee-amount']) }}
{{ BootstrapForm::hidden('transactionFeeRate', null, ['id' => 'transaction-fee-rate']) }}
{{ BootstrapForm::hidden('currentBalance', null, ['id' => 'current-balance']) }}
{{ BootstrapForm::hidden('totalAmount', null, ['id' => 'total-amount']) }}

{{ BootstrapForm::hidden('stripeToken', null, ['id' => 'stripe-token']) }}
{{ BootstrapForm::hidden('paymentMethod', null, ['id' => 'payment-method']) }}

{{ BootstrapForm::label("Payment Transfer Cost") }}:
USD <span id="fee-amount-display"></span>

<br/>

{{ BootstrapForm::label("Total amount to be charged to your account") }}
USD <span id="total-amount-display"></span>

<br/>
<button type="submit" id="stripe-payment" class="btn btn-primary">Pay With Card</button>
<input type="submit" id="paypal-payment" class="btn btn-primary" value="Pay With Paypal" name="submit_paypal">
<input type="submit" id="balance-payment" class="btn btn-primary" value="Pay" name="submit_credit">

<button type="submit" class="btn btn-primary">Accept and Continue</button>

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