@extends('borrower.loan.loan-base')

@section('content')
@parent

@include('borrower.loan.partials.repayment-schedule')
<br><br>
@include('borrower.loan.partials.feedback')

@stop
