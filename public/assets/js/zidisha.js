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
    
    $('body').on('click', '[data-toggle-text]', function() {
        var $this = $(this),
            oldText = $this.text();
        
        $this
            .text($this.data('toggle-text'))
            .data('toggle-text', oldText);
    });

    $('.follow-notifications :checkbox').change(function() {
        var $this = $(this),
            $wrapper = $this.closest('.checkbox'),
            $success = $wrapper.find('.text-success'),
            url = $this.attr('target'),
            data = {};
        
        if ($success.length == 0) {
            $success = $this.closest('.follow-notifications').find('.text-success').last().clone().appendTo($wrapper);
        }
        console.log($success);
        
        data[$this.attr('name')] = $this.is(':checked') ? 1 : 0;
        $success.show();
        $.post(url, data, function() {
            setTimeout(function() {
                $success.hide();
            }, 1500);
        });
        
        return false;
    });
    
    $btnFilters = $('.btn-filter');
    if ($btnFilters.length > 0) {
        $('.btn-filter').each(function() {
            $(this).popover({
                content: $($(this).attr('target')).html(),
                html: true,
                placement: 'bottom'
            });
        });

        $('body').on('click', function (e) {
            $('.btn-filter').each(function () {
                //the 'is' for buttons that trigger popups
                //the 'has' for icons within a button that triggers a popup
                if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                    $(this).popover('hide');
                }
            });
        });


        $btnFilters.on('hide.bs.popover', function () {
            $(this).find('.fa-caret-down').removeClass('fa-caret-up');
        });

        $btnFilters.on('show.bs.popover', function () {
            $(this).find('.fa-caret-down').addClass('fa-caret-up');
        });
        
        $btnFilters.on('click', '.fa-times', function(e) {
            window.navigate($(this).attr('href'));
            return false;
        });
    }

    $('body').on('click', '[data-display=display]', function() {
        $($(this).attr('target')).show();
        $(this).hide();
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
var handler;
    
    $.getScript("https://checkout.stripe.com/checkout.js", function(data, textStatus, jqxhr) {
        handler = StripeCheckout.configure({
            key: config.stripeToken,
            token: function (token, args) {
                $("#stripe-token").val(token.id);
                $("#payment-method").val("stripe");
                $('#funds-upload').submit();
            }
        });
    });

    $('#stripe-payment').click(function (e) {
        handler.open({
            name: 'Zidisha',
            description: 'Payment to Zidisha',
            amount: (parseMoney($("#total-amount").val()) * 100).toFixed(0),
            email: config.email,
            panelLabel: "Pay {{amount}}"
        });
        e.preventDefault();
    });

    var $donationAmount = $('#donation-amount'),
        $donationCreditAmount = $('#donation-credit-amount'),
        $creditAmount = $('#credit-amount'),
        $transactionFeeAmount = $('#transaction-fee-amount'),
        $totalAmount = $('#total-amount'),
        $transactionFeeAmountDisplay = $('#fee-amount-display'),
        $totalAmountDisplay = $('#total-amount-display'),
        feePercentage = Number($('#transaction-fee-rate').val()),
        currentBalance = Number($('#current-balance').val()),
        $paymentMethods = $('#stripe-payment, #paypal-payment'),
        $balanceSubmit = $('#balance-payment'),
        $amount = $('#amount');

    function calculateAmounts() {
        var amount = parseMoney($amount.val()),
            donationAmount = parseMoney($donationAmount.val()),
            creditAmount = (amount >= currentBalance) ? amount - currentBalance : 0,
            newBalance = (amount >= currentBalance) ? 0 : currentBalance - amount,
            transactionFeeAmount = creditAmount * feePercentage / 100,
            donationCreditAmount = (donationAmount >= newBalance) ? donationAmount - newBalance : 0,
            totalAmount = creditAmount + transactionFeeAmount + donationCreditAmount;

        $creditAmount.val(formatMoney(creditAmount));
        $donationCreditAmount.val(formatMoney(donationCreditAmount));
        $transactionFeeAmount.val(formatMoney(transactionFeeAmount));
        $totalAmount.val(formatMoney(totalAmount));
        $transactionFeeAmountDisplay.text(formatMoney(transactionFeeAmount, 2));
        $totalAmountDisplay.text(formatMoney(totalAmount, 2));
        
        if (totalAmount > 0) {
            $paymentMethods.show();
            $balanceSubmit.hide();
            $("#payment-method").val("paypal");
        } else {
            $paymentMethods.hide();
            $balanceSubmit.show();
            $("#payment-method").val("balance");
        }
    }

    $donationAmount.on('keyup', calculateAmounts);
    $amount.on('keyup', calculateAmounts);

    calculateAmounts();
}
