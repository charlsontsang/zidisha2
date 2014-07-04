@extends('layouts.master')

@section('content')

@include('borrower.loan.partials.application-steps')

<div class="row">
    {{ BootstrapForm::open(array('controller' => 'LoanApplicationController@postApplication', 'translationDomain' => 'borrower.loan-application-page')) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::text('summary') }}

    {{ BootstrapForm::textarea('proposal') }}

    {{ BootstrapForm::select('categoryId', $form->getCategories()) }}

    {{ BootstrapForm::text('amount') }}

    {{ BootstrapForm::text('installmentAmount') }}

    {{ BootstrapForm::select('installmentDay', $form->getDays()) }}

    <div class="col-md-7">
        <a href="{{ action('LoanApplicationController@getProfile') }}" class="btn btn-primary">
            Previous
        </a>
    </div>
    <div class="col-md-5">

        {{ BootstrapForm::submit('save') }}

        {{ BootstrapForm::close() }}
    </div>
</div>
@stop
