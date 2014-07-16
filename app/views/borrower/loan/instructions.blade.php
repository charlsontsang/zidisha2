@extends('layouts.master')

@section('page-title')
    @lang("borrower.loan-application.progress-bar.instructions-page")
@stop

@section('content')

@include('borrower.loan.partials.application-steps')

<div class="page-header">
    <h1>@lang("borrower.loan-application.progress-bar.instructions-page")</h1>
</div>


Instructions
<div class="row">
    <div class="col-md-5 col-md-push-7">
        {{ BootstrapForm::open(array('controller' => 'LoanApplicationController@postInstructions', 'translationDomain' => 'borrower.loan-instruction-page')) }}

        {{ BootstrapForm::submit('save') }}

        {{ BootstrapForm::close() }}
    </div>
</div>
@stop
