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
    @lang("borrower.loan-application.confirmation.intro", ['loanLink' => route('loan:index', $loan->getId())])
</p>
<p>
    @lang("borrower.loan-application.confirmation.note", ['days' => \Setting::get('loan.deadline'), 'loanEditLink' => route('borrower:loan-application')])
</p>
<p>
    @lang("borrower.loan-application.confirmation.best-of-luck")
</p>

@stop
