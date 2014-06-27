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

{{ BootstrapForm::text('amount', null, ['id' => 'amount']) }}
{{ BootstrapForm::text('donationAmount', null, ['id' => 'donation-amount']) }}

{{ BootstrapForm::hidden('transactionFee', null, ['id' => 'transaction-fee-amount']) }}
{{ BootstrapForm::hidden('transactionFeeRate', null, ['id' => 'fee-amount-rate']) }}
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
    $(function () {

        var handler = StripeCheckout.configure({
            key: "{{ \Config::get('stripe.public_key') }}",
            token: function (token, args) {
                $("#stripe-token").val(token.id);
                $("#payment-method").val("stripe");
                $('#funds-upload').submit();
            }
        });
        $(function () {
            $('#stripe-payment').click(function (e) {
                handler.open({
                    name: 'Zidisha',
                    description: 'Payment to Zidisha',
                    amount: parseFloat($("#total-amount").val()) * 100,
                    email: "{{ \Auth::user()->getEmail() }}",
                    panelLabel: "Pay @{{amount}}"
                });
                e.preventDefault();
            });
        });

        var $donationAmount = $('#donation-amount'),
            $amount = $('#amount'),
            $transactionFeeAmount = $('#transaction-fee-amount'),
            $totalAmount = $('#total-amount'),
            $transactionFeeAmountDisplay = $('#fee-amount-display'),
            $totalAmountDisplay = $('#total-amount-display'),
            feePercentage = Number($('#fee-amount-rate').val());

        function parseMoney(value) {
            return Number(value.replace(/[^0-9\.]+/g, ""));
        }

        function formatMoney(value) {
            return value.toFixed(2);
        }

        function calculateAmounts() {
            var donationAmount = parseMoney($donationAmount.val()),
                amount = parseMoney($amount.val()),
                transactionFeeAmount = amount * feePercentage,
                totalAmount = amount + transactionFeeAmount + donationAmount;

            $transactionFeeAmount.val(formatMoney(transactionFeeAmount));
            $totalAmount.val(formatMoney(totalAmount));
            $transactionFeeAmountDisplay.text(formatMoney(transactionFeeAmount));
            $totalAmountDisplay.text(formatMoney(totalAmount));
        }

        $donationAmount.on('keyup', calculateAmounts);
        $amount.on('keyup', calculateAmounts);

        calculateAmounts();
    });
</script>
@stop
