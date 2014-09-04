@extends('layouts.master')

@section('page-title')
    @lang("borrower.loan-application.progress-bar.profile-page")
@stop

@section('content')

@include('borrower.loan.partials.application-steps')

<div class="page-header">
    <h1>
        @lang("borrower.loan-application.title.profile-page")
    </h1>
</div>


{{ BootstrapForm::open(array('controller' => 'LoanApplicationController@postProfile', 'translationDomain' => 'borrower.loan-application.profile')) }}
<div class="row">
    <div class="col-md-8">
        {{ BootstrapForm::populate($form) }}

        {{ BootstrapForm::textarea('aboutMe', null, [
            'description' => Lang::get('borrower.loan-application.profile.about-me-description')
        ]) }}

        {{ BootstrapForm::textarea('aboutBusiness', null, [
        'description' => Lang::get('borrower.loan-application.profile.about-business-description')
        ]) }}
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        <a href="{{ action('LoanApplicationController@getInstructions') }}" class="btn btn-primary">
            @lang('borrower.loan-application.previous')
        </a>
    </div>
    <div class="col-xs-6">
        <div class="pull-right">
            {{ BootstrapForm::submit(
                Lang::get('borrower.loan-application.next') . ': ' . Lang::get('borrower.loan-application.title.application-page'),
                ['translationDomain' => false]
            ) }}
        </div>
    </div>
</div>
{{ BootstrapForm::close() }}
@stop