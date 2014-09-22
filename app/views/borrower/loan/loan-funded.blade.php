@extends('borrower.loan.loan-base')

@section('content')
@parent

<div class="panel panel-info">
    <div class="panel-heading">
        <h4>
            {{ \Lang::get('borrower.loan.loan-funded.message') }}
        </h4>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-6">

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
    </div>
</div>

@stop
