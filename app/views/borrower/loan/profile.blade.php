@extends('layouts.master')

@section('page-title')
    @lang("borrower.loan-application.progress-bar.profile-page")
@stop

@section('content')

@include('borrower.loan.partials.application-steps')

<div class="page-header">
    <h1>
        @lang("borrower.loan-application.progress-bar.profile-page")
    </h1>
</div>


<div class="row">
    {{ BootstrapForm::open(array('controller' => 'LoanApplicationController@postProfile', 'translationDomain' => 'borrower.loan-profile-page')) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::textarea('aboutMe') }}

    {{ BootstrapForm::textarea('aboutBusiness') }}
    <div class="col-md-7">
        <a href="{{ action('LoanApplicationController@getInstructions') }}" class="btn btn-primary">
            Previous
        </a>
    </div>
    <div class="col-md-5">

        {{ BootstrapForm::submit('save') }}

        {{ BootstrapForm::close() }}
    </div>
</div>
@stop