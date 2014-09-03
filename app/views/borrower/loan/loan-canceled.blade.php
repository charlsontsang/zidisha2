@extends('borrower.loan.loan-base')

@section('content')
@parent

<p>
    {{ \Lang::get('borrower.loan.canceled.cancel-message') }}
</p>
@stop
