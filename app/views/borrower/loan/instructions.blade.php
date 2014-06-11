@extends('layouts.master')

@section('content')

@include('borrower.loan.partials.application-steps')

Instructions

<div class="row">
    <div class="col-md-5 col-md-push-7">
        {{ BootstrapForm::open(array('controller' => 'LoanApplicationController@postInstructions', 'translationDomain' => 'borrower.loan-profile-page')) }}

        {{ BootstrapForm::submit('save') }}

        {{ BootstrapForm::close() }}
    </div>
</div>
@stop