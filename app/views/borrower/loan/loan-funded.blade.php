@extends('borrower.loan.loan-base')

@section('content')
@parent

<div class="callout callout-info">
    <p>{{ \Lang::get('borrower.loan.loan-funded.message') }}</p>
</div>

<div class="row">
    <div class="col-sm-6">
        <h4>Details</h4>

        @include('borrower.loan.partials.loan-information-fundraising', compact('loan', 'installmentCalculator'))

        <h4>
            @lang('borrower.loan.repayment-schedule.title')
        </h4>
        
        @include('borrower.loan.partials.repayment-schedule-installments', compact('repaymentSchedule'))
    </div>
    
    <div class="col-xs-6">
        @if($loan->getAcceptBidsNote())
            <h4>
                @lang('borrower.loan.loan-funded.accept-bids-note')
            </h4>
            
            {{{ $loan->getAcceptBidsNote() }}}
        @endif
    </div>
</div>

@stop
