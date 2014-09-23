<div class="lend-form">
    <div id="lend-form-initial" style="{{ \Auth::check() && \Auth::user()->isLender() && \Request::query('amount') ? 'display:none' : '' }}">
        @include('loan/loan/partials/progress', [ 'loan' => $loan ])

        {{ BootstrapForm::open(array('route' => ['loan:place-bid', $loan->getId()], 'translationDomain' => 'bid', 'id' => 'funds-upload')) }}
        {{ BootstrapForm::populate($placeBidForm) }}

        <div id="lend-form-fields" class="row">
            <div class="col-xs-6" style="padding-right: 5px">
                {{ BootstrapForm::text('amount', Request::query('amount'), [
                'id' => 'amount',
                'label' => false,
                'prepend' => '$'
                ]) }}
                <div class="text-center text-light">
                    Loan Amount
                </div>
            </div>
            <div class="col-xs-6" style="padding-left: 5px">
                {{ BootstrapForm::select('interestRate', $placeBidForm->getRates(), Request::query('interestRate'), [
                'label' => false
                ]) }}
                <div class="text-center text-light">
                    Interest
                </div>
            </div>
        </div>

        @if (\Auth::check())
        @if (\Auth::user()->isLender() && !Request::query('amount'))
        <button id="lend-action" type="button" class="btn btn-primary btn-block">Lend</button>
        @endif
        @else
        <a href="{{ route('lender:join') }}" id="join-lend" class="btn btn-primary btn-block" data-toggle="modal" data-target="#join-modal">Lend</a>
        @endif
    </div> <!-- /lend-form-initial -->

    <div id="lend-details" class="lend-details" style="{{ \Auth::check() && \Auth::user()->isLender() && \Request::query('amount') ? '' : 'display:none' }}">
        {{ BootstrapForm::hidden('creditAmount', null, ['id' => 'credit-amount']) }}

        {{ BootstrapForm::hidden('donationCreditAmount', null, ['id' => 'donation-credit-amount']) }}
        {{ BootstrapForm::hidden('loanId', $loan->getId()) }}

        {{ BootstrapForm::hidden('transactionFee', null, ['id' => 'transaction-fee-amount']) }}
        {{ BootstrapForm::hidden('transactionFeeRate', null, ['id' => 'transaction-fee-rate']) }}
        {{ BootstrapForm::hidden('currentBalance', null, ['id' => 'current-balance']) }}
        {{ BootstrapForm::hidden('isLenderInviteCredit', null, ['id' => 'is-lender-invite-credit']) }}
        {{ BootstrapForm::hidden('totalAmount', null, ['id' => 'total-amount']) }}

        {{ BootstrapForm::hidden('stripeToken', null, ['id' => 'stripe-token']) }}
        {{ BootstrapForm::hidden('paymentMethod', null, ['id' => 'payment-method']) }}

        <table class="table">
            <tbody>
            <tr>
                <td colspan="2" style="width: 67%;">Loan for {{ $borrower->getFirstName() }}</td>
                <td>$<span id="amount-display"></span></td>
            </tr>
            <tr>
                <td>
                    Donation{{ BootstrapHtml::tooltip('borrower.tooltips.loan.donation-to-zidisha') }}
                </td>
                <td>
                    {{ BootstrapForm::select(null, array('.2' => '20%', '.15' => '15%', '.1' => '10%', 'other' => 'Other', '0' => 'None'), '.15', [
                        'label' => false,
                        'id' => 'donation-percent',
                        'style' => 'width: 100%; height: 25px',
                    ]) }}
                </td>
                <td style="width: 100px;">
                    {{ BootstrapForm::text('donationAmount', null, [
                    'id'      => 'donation-amount',
                    'label'   => false,
                    ]) }}
                </td>
            </tr>
            <tr style="display: none;">
                <td colspan="2">
                    Credit card fee
                    {{ BootstrapHtml::tooltip('borrower.tooltips.loan.credit-card-fee') }}
                </td>
                <td>$<span id="fee-amount-display"></span></td>
            </tr>
            @if($placeBidForm->getLenderInviteCredit()->isPositive())
            <tr>
                <td colspan="2">Lender invite credit</td>
                <td>${{ number_format($placeBidForm->getLenderInviteCredit()->getAmount(), 2, '.', '') }}</td>
            </tr>
            @elseif($placeBidForm->getCurrentBalance()->isPositive())
            <tr>
                <td colspan="2">Lending Credit</td>
                <td>${{ number_format($placeBidForm->getCurrentBalance()->getAmount(), 2, '.', '') }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="2"><strong>Total</strong></td>
                <td>$<strong><span id="total-amount-display"></span></strong></td>
            </tr>
            </tbody>
        </table>

        @include('partials/payment-buttons')

        <input type="submit" id="balance-payment" class="btn btn-primary btn-block" value="Confirm" name="submit_credit">

    </div>
    {{ BootstrapForm::close() }}
</div>

@section('script-footer')
<script type="text/javascript">
    $(function () {
        var $amount = $('#amount');
        paymentForm({
            stripeToken: "{{ \Zidisha\Admin\Setting::get('stripe.publicKey') }}",
            email: "{{ \Auth::check() ? \Auth::user()->getEmail() : '' }}",
            amount: $amount
        });

        $('#amount-display').text(formatMoney(parseMoney($amount.val()), 2));
        $amount.on('change', function() {
            $('#amount-display').text(formatMoney(parseMoney($amount.val()), 2));
        });
        $amount.on('keyup keypress', function(e) {
            if (e.which  == 13) {
                e.preventDefault();
                return false;
            }
        });

        $('#join-lend').on('click', function() {
            var data = $(this).closest('form').serialize();
            // https://github.com/laravel/framework/issues/4576
            setTimeout(function() {
                $.post("{{ route('lender:join-lend') }}", data);
            }, 1000);
        });
        $('#mobile-lend-btn').on('click', function() {
            var $modal = $($('#modal-template').html()),
                $form = $('.bid-form'),
                $formParent = $form.parent();

            $form.css('width', '');
            $modal.find('.modal-body').append($form);
            $modal.modal();

            $modal.on('hidden.bs.modal', function (e) {
                $formParent.prepend($form);
                $form.css('width', $formParent.outerWidth());
            })
        });
    });
</script>
<script type="text/html" id="modal-template">
    <div class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
</script>
@append
