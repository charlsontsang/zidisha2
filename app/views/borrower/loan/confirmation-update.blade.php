@extends('layouts.master')

@section('page-title')
@lang("borrower.loan-application.title.confirmation-page")
@stop

@section('content')
<div class="page-header">
    <h1>
        @lang("borrower.loan-application.title.confirmation-page")
    </h1>
</div>

<p>
    @lang("borrower.loan-application.confirmation-update.intro", ['loanLink' => route('loan:index', $loan->getId())])
</p>

@stop
