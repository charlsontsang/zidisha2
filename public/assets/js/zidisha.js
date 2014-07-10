$(function () {

    var $comments = $('.comments');

    $comments.on('click', '.comment-action', function () {
        var $this = $(this),
            $forms = $this.closest('.comment').find('.comment-forms');

        $forms.find('.comment-form').hide();
        $forms.find('[data-comment-action=' + $this.attr('target') + ']').show();

        return false;
    });

    $comments.on('click', '.comment-share', function () {
        $(this).next().toggle();
        return false;
    });

    $comments.on('click', '.comment-original-message', function () {
        $(this).closest('p').next().toggle();
        return false;
    });

    var commentUploadTemplate = $('#comment-upload-input-template').html();

    $comments.on('click', '.comment-upload-add-more', function () {
        $(this).closest('.comment').find('.comment-upload-inputs').append($(commentUploadTemplate));
        return false;
    });

    var $borrowerEditForm = $('.borrower-edit-form');

    var borrowerUploadTemplate = $('#borrower-upload-input-template').html();
    $borrowerEditForm.on('click', '.borrower-upload-add-more', function () {
        $borrowerEditForm.find('.borrower-upload-inputs').prepend($(borrowerUploadTemplate));
        return false;
    });
});

function parseMoney(value) {
    return Number(value.replace(/[^0-9\.]+/g, ""));
}

function formatMoney(value, scale) {
    scale = scale || 4;
    return value.toFixed(scale);
}

function paymentForm(config) {

    var handler = StripeCheckout.configure({
        key: config.stripeToken,
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
                amount: (parseMoney($("#total-amount").val()) * 100).toFixed(0),
                email: config.email,
                panelLabel: "Pay @{{amount}}"
            });
            e.preventDefault();
        });
    });

    var $donationAmount = $('#donation-amount'),
        $creditAmount = $('#credit-amount'),
        $transactionFeeAmount = $('#transaction-fee-amount'),
        $totalAmount = $('#total-amount'),
        $transactionFeeAmountDisplay = $('#fee-amount-display'),
        $totalAmountDisplay = $('#total-amount-display'),
        feePercentage = Number($('#fee-amount-rate').val()),
        currentBalance = Number($('#current-balance').val()),
        $paymentMethods = $('#stripe-payment, #paypal-payment'),
        $creditSubmit = $('#credit-payment'),
        $amount = config.amount;

    function calculateAmounts() {
        var donationAmount = parseMoney($donationAmount.val()),
            creditAmount = parseMoney($creditAmount.val()),
            transactionFeeAmount = creditAmount * feePercentage,
            totalAmount = creditAmount + transactionFeeAmount + donationAmount;

        $transactionFeeAmount.val(formatMoney(transactionFeeAmount));
        $totalAmount.val(formatMoney(totalAmount));
        $transactionFeeAmountDisplay.text(formatMoney(transactionFeeAmount, 2));
        $totalAmountDisplay.text(formatMoney(totalAmount, 2));
        
        if (totalAmount > 0) {
            $paymentMethods.show();
            $creditSubmit.hide();
        } else {
            $paymentMethods.hide();
            $creditSubmit.show();
        }
    }

    $donationAmount.on('keyup', calculateAmounts);
    $amount.on('keyup', function() {
        var amount = parseMoney($amount.val()),
            creditAmount = (amount >= currentBalance) ? amount - currentBalance : 0;
        $creditAmount.val(creditAmount);
        calculateAmounts();
    });

    calculateAmounts();
}