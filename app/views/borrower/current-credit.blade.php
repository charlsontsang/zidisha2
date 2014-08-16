@extends('layouts.master')

@section('page-title')
{{ \Lang::get('borrower.loan-application.current-credit.title') }}
@stop

@section('content')
<div class="page-header">
    <h1>
        {{ \Lang::get('borrower.loan-application.current-credit.title') }}
    </h1>
</div>
<div>
    <p>{{ $beginning }}</p>
    <p>{{ $note }}</p>
    <p>{{ $inviteCredit }}</p>
    <p>{{ $volunteerMentorCredit }}</p>
    <p>{{ $end }}</p>
</div>
@stop
