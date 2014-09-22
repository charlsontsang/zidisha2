@extends('layouts.side-menu-simple')

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
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">
            Add funds to your lending account
        </h3>
    </div>
    <div class="panel-body">

        <p>Current lending credit: <strong>{{ $currentBalance }}</strong></p>
        <div class="lend-form">
            <div class="lend-details">
   
                {{ BootstrapForm::open(array('route' => 'lender:post-funds', 'translationDomain' => 'fund', 'id' => 'funds-upload')) }}
                {{ BootstrapForm::populate($form) }}

                {{ BootstrapForm::hidden('creditAmount', null, ['id' => 'credit-amount']) }}
                {{ BootstrapForm::hidden('donationCreditAmount', null, ['id' => 'donation-credit-amount']) }}

                {{ BootstrapForm::hidden('donationAmount', null, ['id' => 'donation-amount']) }}

                {{ BootstrapForm::hidden('transactionFee', null, ['id' => 'transaction-fee-amount']) }}
                {{ BootstrapForm::hidden('transactionFeeRate', null, ['id' => 'transaction-fee-rate']) }}
                {{ BootstrapForm::hidden('currentBalance', 0, ['id' => 'current-balance']) }}
                {{ BootstrapForm::hidden('totalAmount', null, ['id' => 'total-amount']) }}

                {{ BootstrapForm::hidden('stripeToken', null, ['id' => 'stripe-token']) }}
                {{ BootstrapForm::hidden('paymentMethod', null, ['id' => 'payment-method']) }}

                <table class="table">
                    <tbody>
                    <tr>
                        <td width="67%">Lending Credit</td>
                        <td>
                            {{ BootstrapForm::text('amount', null, ['label' => '', 'id' => 'amount', 'prepend' => '$']) }}
                        </td>
                    </tr>
                    <tr style="display: none;">
                        <td>
                            Credit card fee
                            {{ BootstrapHtml::tooltip('borrower.tooltips.loan.credit-card-fee') }}
                        </td>
                        <td>$<span id="fee-amount-display"></span></td>
                    </tr>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td>$<strong><span id="total-amount-display"></span></strong></td>
                    </tr>
                    </tbody>
                </table>

                @include('partials/payment-buttons')

                <input type="submit" id="balance-payment" class="btn btn-primary btn-block" value="Confirm" name="submit_credit">

            </div>
            {{ BootstrapForm::close() }}
        </div>
    </div>
</div>

<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">
            Redeem a gift card
        </h3>
    </div>
    <div class="panel-body">

        {{ BootstrapForm::open(array('route' => 'lender:post-redeem-card')) }}

        {{ BootstrapForm::text('redemptionCode', null, ['label' => 'Enter Redemption Code']) }}

        <button id="stripe-payment" class="btn btn-primary">Redeem</button>

        {{ BootstrapForm::close() }}
    </div>
</div>

<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">
            Withdraw your lending credit
        </h3>
    </div>
    <div class="panel-body">

        <p>
            Current lending credit: <strong>{{ $currentBalance }}</strong>
        </p>

        {{ BootstrapForm::open(array('route' => 'lender:post-withdraw-funds')) }}

        {{ BootstrapForm::text('paypalEmail', null, ['label' => 'Your PayPal Account Address']) }}
        {{ BootstrapForm::text('withdrawAmount', null, ['label' => 'Amount to Withdraw']) }}

        <button id="stripe-payment" class="btn btn-primary">Submit</button>

        {{ BootstrapForm::close() }}
    </div>
</div>

@stop

@section('script-footer')
<script src="https://checkout.stripe.com/checkout.js"></script>
<script type="text/javascript">
    $(function() {
        var $amount = $('#amount');
        paymentForm({
            stripeToken: "{{ \Zidisha\Admin\Setting::get('stripe.publicKey') }}",
            email: "{{ \Auth::check() ? \Auth::user()->getEmail() : '' }}",
            amount: $amount
        })
    });
</script>
@stop
