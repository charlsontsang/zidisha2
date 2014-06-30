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

function formatMoney(value) {
    return value.toFixed(2);
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
        $amount = $('#amount'),
        $transactionFeeAmount = $('#transaction-fee-amount'),
        $totalAmount = $('#total-amount'),
        $transactionFeeAmountDisplay = $('#fee-amount-display'),
        $totalAmountDisplay = $('#total-amount-display'),
        feePercentage = Number($('#fee-amount-rate').val());

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

}