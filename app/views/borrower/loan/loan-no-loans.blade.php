@extends('layouts.master')

@section('content')

<h1>
    @lang('borrower.loan.page.no-loan')
</h1>

<a class="btn btn-primary" href="{{ route('borrower:loan-application') }}">
    @lang('borrower.loan.page.apply')
</a>

<p>
    {{ \Lang::get('borrower.loan.no-loan.no-loan-message') }}
</p>
@stop
