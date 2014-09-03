@extends('borrower.loan.loan-base')

@section('content')
@parent

@include('borrower.loan.partials.repayment-schedule')

<div>
    <p>{{ \Lang::get('borrower.loan.fully-funded.message') }}</p>
</div>

@stop
