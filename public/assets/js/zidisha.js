$(function () {

    function flash(message, level, delay) {
        $.growl(message, {
            type: level || 'success',
            allow_dismiss: true,
            delay: delay || 0,
            z_index: 2000
        });
    }

    $('[data-toggle="tooltip"]').tooltip();

    // COMMENTS

    var $comments = $('.comments');

    $comments.on('click', '.comment-action', function () {
        var $this = $(this),
            $forms = $this.closest('.comment').find('.comment-forms:first');

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
        $(this).closest('.comment').find('.comment-upload-inputs').prepend($(commentUploadTemplate));
        return false;
    });

    $comments.on('submit', 'form', function() {
        var messageBody = $(this).find(':input[name=message]'),
            submitButton = $(this).find(':submit');

        if (!messageBody.length) return;
        
        messageBody.parent().removeClass('has-error');

        if (messageBody.val() == '' || messageBody.val() == null) {
            messageBody.parent().addClass('has-error');
            return false;
        }

        submitButton.button('loading');
    });
    
    $body = $('body');
    
    $body.on('click', '.share-popup', function() {
        var shareWindow = window.open(
            $(this).attr('href'),
            'shareWindow' + Math.random(),
                'height=450, width=550, top=' + ($(window).height() / 2 - 275) + ', left=' + ($(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0'
        );

        return false;
    });

    var $borrowerEditForm = $('.borrower-edit-form');

    var borrowerUploadTemplate = $('#borrower-upload-input-template').html();
    $borrowerEditForm.on('click', '.borrower-upload-add-more', function () {
        $borrowerEditForm.find('.borrower-upload-inputs').prepend($(borrowerUploadTemplate));
        return false;
    });
    
    $body.on('click', '[data-toggle-text]', function() {
        var $this = $(this),
            oldText = $this.text();
        
        $this
            .text($this.data('toggle-text'))
            .data('toggle-text', oldText);
    });
    
    // FOLLOWERS

    $('.follow-notifications :checkbox').change(function() {
        var $this = $(this),
            url = $this.attr('target'),
            data = {};

        data[$this.attr('name')] = $this.is(':checked') ? 1 : 0;

        $this.attr('disabled', 'disabled');
        
        $.post(url, data, function(res) {
            flash(res.message, 'success', 4000);
        }, 'json')
            .always(function() {
                $this.removeAttr('disabled');
            })
            .fail(function() {
                flash('Oops, something went wrong', 'danger');
            });
        
        return false;
    });
    
    $('[data-follow="follow"]').on('click', function(e) {
        var $this = $(this),
            url = $this.attr('href');
        
        e.preventDefault();
        
        $this.attr('disabled', 'disabled');

         $.post(url, function(res) {            
            $this
                .hide()
                .parent().find('.follow-settings').show();
        }, 'json')
             .always(function() {
                 $this.removeAttr('disabled');
             })
             .fail(function() {
                 flash('Oops, something went wrong', 'danger');
             });
    });

    $('[data-follow="unfollow"]').on('click', function(e) {
        var $this = $(this),
            url = $this.attr('href');

        e.preventDefault();

        $this.attr('disabled', 'disabled');

        $.post(url, function(res) {
            var $wrapper = $this.closest('.follow-settings');
            
            $wrapper.hide();
            $wrapper.find(':checkbox').prop('checked', true);
            
            if ($this.attr('data-follow-enabled') == 'enabled') {
                $wrapper.parent().find('[data-follow="follow"]').show();
            }
        }, 'json')
            .always(function() {
                $this.removeAttr('disabled');
            })
            .fail(function() {
                flash('Oops, something went wrong', 'danger');
            });
    });
    
    // FILTER BAR
    
    $btnFilters = $('.btn-filter');
    if ($btnFilters.length > 0) {
        $btnFilters.each(function() {
            $(this).popover({
                content: $($(this).attr('target')).html(),
                html: true,
                placement: 'bottom'
            });
        });

        $body.on('click', function (e) {
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

    $body.on('click', '[data-display=display]', function() {
        $($(this).attr('target')).show();
        $(this).hide();
        return false;
    });

    $body.on('click', '[data-dismiss=removeFile]', function() {
        $(this).closest('.file-input-block').remove();
        return false;
    });

    // Scrollspy
    var $window = $(window);
    var $body   = $(document.body);

    $body.scrollspy({
        target: '.sidenav'
    });
    $window.on('load', function () {
        $body.scrollspy('refresh');
    });

    $('.sidenav li').on('click', function () {
        $(this).prev().removeClass( "active");
        $(this).addClass( "active" );
    });

    // Sidenav affixing
    setTimeout(function () {
        var $sideBar = $('.sidenav');

        $sideBar.affix({
            offset: {
                top: function () {
                    var offsetTop      = $sideBar.offset().top;
                    // TODO temp fix for lend box
                    var sideBarMargin  = 23;//parseInt($sideBar.children(0).css('margin-top'), 10);
                    
                    return (this.top = offsetTop - sideBarMargin);
                },
                bottom: function () {
                    return (this.bottom = $('.footer').outerHeight(true));
                }
            }
        });
        
        $sideBar.on('affix.bs.affix', function() {
            if ($sideBar.next().length) {
                $sideBar.next().css({
                    position: 'absolute',
                    top: $sideBar.next().position().top
                });
            }
        });

        $sideBar.on('affix-top.bs.affix', function() {
            if ($sideBar.next().length) {
                $sideBar.next().css({
                    position: 'static',
                    top: 0
                });
            }
        });
    }, 100);
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
        $transactionFeeAmountDisplay.closest('tr')[transactionFeeAmount > 0 ? 'show' : 'hide']();
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
