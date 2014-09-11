@extends('borrower.loan.loan-base')

@section('content')
@parent

@include('borrower.dashboard.partials.loan-defaulted')

<div class="row">
    <div class="col-xs-12 col-sm-6">
        @include('loan.partials.repaid-bar', compact('loan'))
    </div>
</div>

<h4>
    @lang('borrower.loan.repayment-schedule.title')
</h4>

@include('borrower.loan.partials.repayment-schedule')

@stop
