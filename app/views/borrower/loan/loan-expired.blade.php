@extends('borrower.loan.loan-base')

@section('content')
@parent

<p>
    {{ \Lang::get('borrower.loan.expired.expire-message') }}
</p>
@stop
