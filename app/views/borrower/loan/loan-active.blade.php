@extends('borrower.loan.loan-base')

@section('content')
@parent

<div class="row">
    <div class="col-xs-12 col-sm-6">
        <br/>
        <br/>
        @include('loan.partials.repaid-bar', compact('loan'))
        <br/>
    </div>
    <div class="col-xs-12 col-sm-6">
        @include('borrower.loan.next-installment', compact('repaymentSchedule'))     
    </div>
</div>

<h4>
    @lang('borrower.loan.repayment-schedule.title')
</h4>

@include('partials.repayment-schedule-table', compact('repaymentSchedule'))

@stop
