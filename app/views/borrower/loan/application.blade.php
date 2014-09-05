@extends('layouts.master')

@section('page-title')
    @lang("borrower.loan-application.progress-bar.application-page")
@stop

@section('content')

@include('borrower.loan.partials.application-steps')

<div class="page-header">
    <h1>
        @lang("borrower.loan-application.title.application-page")
    </h1>
</div>

{{ BootstrapForm::open(array('controller' => 'LoanApplicationController@postApplication', 'translationDomain' => 'borrower.loan-application.application')) }}
{{ BootstrapForm::populate($form) }}
<div class="row">
    <div class="col-md-10">        
        <p>
            @lang('borrower.loan-application.application.intro', ['link' => route('lend:index')])
        </p>
    
        @if($registrationFee->isPositive())
        <div class="alert alert-warning">
            @lang('borrower.loan-application.application.registration-fee-note', [
                'amount' => $registrationFee,
            ])
        </div>
        @endif
    
        <p>
            <label for="amount" class="control-label">
                @lang('borrower.loan-application.application.amount')
            </label>
        </p>
    
        <p>
            @lang('borrower.loan-application.application.amount-description', [
                'amount' => '123',
                'currency' => 'TODO',
            ])
            <br/>
            <a href="{{ route('borrower:credit') }}" target="_blank">
                @lang('borrower.loan-application.application.amount-credit-limit')
            </a>
        </p>
    
        <p>
            @lang('borrower.loan-application.application.amount-note')
        </p>
    
        {{ BootstrapForm::select('amount', $form->getLoanAmountRange(), null, [
            'label' => false,
            'id' => 'amount',
        ]) }}
    
        <p>
            <label for="installmentAmount" class="control-label">
                @lang("borrower.loan-application.application.installment-amount-$installmentPeriod")
            </label>
        </p>
    
        <p>
            @lang("Borrower.loan-application.application.installment-amount-$installmentPeriod-description")
        </p>
    
        <p> 
            <em>
                @lang("borrower.loan-application.application.installment-amount-$installmentPeriod-tip")            
            </em>
        </p>
        
        {{ BootstrapForm::select('installmentAmount', $form->getInstallmentAmountRange(), null, [
            'label' => false,
            'id' => 'installment-amount',
        ]) }}
    
        <p>
            <label for="installmentDay" class="control-label">
                @lang('borrower.loan-application.application.installment-day')
            </label>
        </p>
    
        <p>
            @lang("Borrower.loan-application.application.installment-day-$installmentPeriod-description")
        </p>
    
        {{ BootstrapForm::select('installmentDay', $form->getDays(), null, [
            'label' => false,
        ]) }}
    
        <p>
            <label for="categoryId" class="control-label">
                @lang('borrower.loan-application.application.category-id')
            </label>
        </p>
    
        <p>
            @lang("Borrower.loan-application.application.category-id-description")
        </p>
    
        {{ BootstrapForm::select('categoryId', $form->getCategories(), null, [
            'label' => false,
        ]) }}
    
        <p>
            <label for="summary" class="control-label">
                @lang('borrower.loan-application.application.summary')
            </label>
        </p>
    
        <p>
            @lang("Borrower.loan-application.application.summary-description")
        </p>
        
        <p>
            <em>
                @lang("Borrower.loan-application.application.summary-tip")            
            </em>
        </p>
    
        {{ BootstrapForm::text('summary', null, [
            'label' => false,
            'style' => 'max-width:100%'
        ]) }}
    
        <p>
            <label for="proposal" class="control-label">
                @lang('borrower.loan-application.application.proposal')
            </label>
        </p>
    
        <p>
            @lang("Borrower.loan-application.application.proposal-description")
        </p>
    
        <p>
            <em>
                @lang("Borrower.loan-application.application.proposal-tip")
            </em>
        </p>
    
        <p>
            @lang("Borrower.loan-application.application.proposal-example")
        </p>
    
        {{ BootstrapForm::textarea('proposal', null, [
            'label' => false,
            'style' => 'max-width:100%'
        ]) }}
    </div>
</div>
<div class="row">

    <div class="col-xs-6">
        <a href="{{ action('LoanApplicationController@getProfile') }}" class="btn btn-primary">
            @lang('borrower.loan-application.previous')
        </a>
    </div>
    
    <div class="col-xs-6">
        <div class="pull-right">
            {{ BootstrapForm::submit(
                Lang::get('borrower.loan-application.next') . ': ' . Lang::get('borrower.loan-application.title.publish-page'),
                ['translationDomain' => false]
            ) }}
        </div>
    </div>
</div>
{{ BootstrapForm::close() }}
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
@append