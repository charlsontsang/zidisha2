@extends('layouts.master')

@section('page-title')
Join the global P2P microlending movement
@stop

@section('content')
<h1>Gift Card Terms and Conditions:</h1>

<p>1. A recipient may redeem a gift card by placing a loan bid, then entering the card’s redemption code while completing the
    transaction in the Lending Cart page. When redeemed, the full value of the gift card will be credited to the recipient’s
    lender
    account.</p>

<p>2. If the gift card is not redeemed within twelve months of the card purchase date, the card will automatically convert to an
    unrestricted donation to Zidisha Inc., and can no longer be redeemed by the recipient.</p>

<p>3. It is the responsibility of the purchaser to exercise appropriate caution in safeguarding a gift card and its redemption
    code.
    Gift cards are non-refundable, and replacements cannot be issued for a gift card that is lost or redeemed by someone other
    than the intended recipient.</p>

<p>4. The utilization of gift cards is subject to the general <a href="{{ route('page:terms-of-use') }}">Terms and Conditions</a>
    governing use of the <a
        href="www.zidisha.org">www.zidisha.org</a>
    website
    .</p>


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