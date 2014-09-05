@extends('layouts.master')

@section('page-title')
Gift Card Purchase
@stop

@section('content')
</div> <!-- /container -->
<div class="container-fluid lend">
    <div class="panel panel-default lend-details lend-form pay-giftcard">

        {{ BootstrapForm::open(array('route' => 'lender:gift-cards:post-terms-accept', 
        'id' => 'funds-upload')) }}
        {{ BootstrapForm::populate($paymentForm) }}

         <table class="table">
            <tbody>
                <tr>
                    <td>
                        @if(!empty($recipientName))
                            Gift card for {{ $recipientName }}
                        @else
                            Gift card
                        @endif
                    </td>
                    <td>${{ $amount }}</span></td> 
                </tr>
                <tr>
                    <td>
                        Donation to Zidisha
                        {{ BootstrapHtml::tooltip('borrower.tooltips.loan.donation-to-zidisha') }}
                    </td>
                    <td style="width: 100px;">
                        {{ BootstrapForm::text('donationAmount', null, [
                            'id'      => 'donation-amount',
                            'label'   => false,
                        ]) }}
                    <!-- TO DO: make the default 15% of the loan amount -->
                    </td>
                </tr>
                <tr>
                    <td>
                        Credit card fee
                        {{ BootstrapHtml::tooltip('borrower.tooltips.loan.credit-card-fee') }}
                    </td>
                    <td>$<span id="fee-amount-display"></span></td>
                </tr>
                <tr>
                    <td>Current Balance</td>
                    <td>TO DO</td>
                </tr>
                <tr>
                    <td><strong>Total</strong></td>
                    <td>$<strong><span id="total-amount-display"></span></strong></td>
                </tr>
            </tbody>
        </table>

        {{ BootstrapForm::hidden('amount', $amount, ['id' => 'amount']) }}

        {{ BootstrapForm::hidden('creditAmount', null, ['id' => 'credit-amount']) }}
        {{ BootstrapForm::hidden('donationCreditAmount', null, ['id' => 'donation-credit-amount']) }}

        {{ BootstrapForm::hidden('transactionFee', null, ['id' => 'transaction-fee-amount']) }}
        {{ BootstrapForm::hidden('transactionFeeRate', null, ['id' => 'transaction-fee-rate']) }}
        {{ BootstrapForm::hidden('currentBalance', null, ['id' => 'current-balance']) }}
        {{ BootstrapForm::hidden('totalAmount', null, ['id' => 'total-amount']) }}

        {{ BootstrapForm::hidden('stripeToken', null, ['id' => 'stripe-token']) }}
        {{ BootstrapForm::hidden('paymentMethod', null, ['id' => 'payment-method']) }}

        <p>
            By purchasing a gift card, I agree to the <a target="_blank" href="/terms-of-use#gift-card">Gift Card Terms of Use</a>.
        </p>
                        
        <button type="submit" id="stripe-payment" class="btn btn-primary btn-block">Pay With Card</button>
        <input type="submit" id="paypal-payment" class="btn btn-primary btn-block" value="Pay With Paypal" name="submit_paypal">
        <input type="submit" id="balance-payment" class="btn btn-primary btn-block" value="Pay" name="submit_credit">

        {{ BootstrapForm::close() }}
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