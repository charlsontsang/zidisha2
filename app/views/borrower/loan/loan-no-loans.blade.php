@extends('borrower.loan.loan-base')

@section('content')
@parent

<p>
    {{ \Lang::get('borrower.loan.no-loan.no-loan-message') }}
</p>
@stop
