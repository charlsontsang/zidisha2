@extends('borrower.loan.loan-base')

@section('page-content')
@parent

<div class="panel panel-info">
    <div class="panel-heading">
        <h4>
            {{ \Lang::get('borrower.loan.loan-funded.message') }}
        </h4>
    </div>
    <div class="panel-body">
        @include('borrower.loan.partials.loan-information-fundraising', compact('loan', 'installmentCalculator'))

        <h4>
            @lang('borrower.loan.repayment-schedule.title')
        </h4>
        
        @include('borrower.loan.partials.repayment-schedule-installments', compact('repaymentSchedule'))

        @if($loan->getAcceptBidsNote())
            <h4>
                @lang('borrower.loan.loan-funded.accept-bids-note')
            </h4>
            
            {{{ $loan->getAcceptBidsNote() }}}
        @endif
    </div>
</div>

@stop
