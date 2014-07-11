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


{{ BootstrapForm::open(array('route' => 'lender:gift-cards:terms-accept', 'translationDomain' => 'fund', 'id' => 'funds-upload')) }}

@if($enoughBalance == 0)

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
<button type="submit" id="stripe-payment" class="btn btn-primary">Pay With Card</button>
<input type="submit" id="paypal-payment" class="btn btn-primary" value="Pay With Paypal" name="submit_paypal">


@else
<button type="submit" class="btn btn-primary">Accept and Continue</button>
@endif
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