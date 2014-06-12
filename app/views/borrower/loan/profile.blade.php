@extends('layouts.master')

@section('content')

@include('borrower.loan.partials.application-steps')

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