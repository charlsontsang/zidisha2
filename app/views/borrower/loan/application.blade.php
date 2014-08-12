@extends('layouts.master')

@section('page-title')
    @lang("borrower.loan-application.progress-bar.application-page")
@stop

@section('content')

@include('borrower.loan.partials.application-steps')

<div class="page-header">
    <h1>
        @lang("borrower.loan-application.progress-bar.application-page")
    </h1>
</div>

<div class="row">
    {{ BootstrapForm::open(array('controller' => 'LoanApplicationController@postApplication', 'translationDomain' => 'borrower.loan-application-page')) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::text('summary') }}

    {{ BootstrapForm::textarea('proposal') }}

    {{ BootstrapForm::select('categoryId', $form->getCategories()) }}

    {{ BootstrapForm::select('amount', $form->getLoanAmountRange(), null, ['id' => 'amount']) }}

    {{ BootstrapForm::select('installmentAmount', $form->getInstallmentAmountRange(), null, ['id' => 'installment-amount']) }}

    {{ BootstrapForm::select('installmentDay', $form->getDays()) }}

    <div class="col-md-7">
        <a href="{{ action('LoanApplicationController@getProfile') }}" class="btn btn-primary">
            Previous
        </a>
    </div>
    <div class="col-md-5">

        {{ BootstrapForm::submit('save') }}

        {{ BootstrapForm::close() }}
    </div>
</div>
@stop

@section('script-footer')
<script type="text/javascript">
    $(function () {
        var url = "{{ action('LoanApplicationController@getInstallmentAmountRange') }}";
        $('#amount').on('change', function() {
            var $this = $(this),
                $installmentAmount = $('#installment-amount');
            $installmentAmount.attr('disabled', 'disabled');
            $.get(url + '?amount=' + $this.val(), function(res) {
                $installmentAmount.empty();
                $.each(res, function(key, option) { 
                    $installmentAmount.append($("<option></option>")
                        .attr("value", option[1]).text(option[0]));
                });
                $installmentAmount.removeAttr('disabled');
            });
        });
    });
</script>
@stop