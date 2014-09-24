@extends('borrower.loan.loan-base')

@section('page-content')
@parent

<div class="panel panel-info">
    <div class="panel-body">
        @include('loan.partials.repaid-bar', compact('loan'))
        <br/>
        @include('borrower.loan.next-installment', compact('repaymentSchedule')) 
    </div>
</div>
<div class="panel panel-info">
    <div class="panel-heading">
        <h4>
            @lang('borrower.loan.repayment-schedule.title')
        </h4>
    </div>
    <div class="panel-body">
        @include('partials.repayment-schedule-table', compact('repaymentSchedule'))
    </div>
</div>

@stop
