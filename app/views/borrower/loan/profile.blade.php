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


{{ BootstrapForm::open([
    'controller' => 'LoanApplicationController@postProfile',
    'translationDomain' => 'borrower.loan-application.profile',
    'files' => true
]) }}
{{ BootstrapForm::populate($form) }}
<div class="row">
    <div class="col-md-8">
        <p>
            <label for="picture" class="control-label">
                @lang('borrower.loan-application.profile.picture')
            </label>
        </p>

        <p>
            <em>
                @lang('borrower.loan-application.profile.picture-description')
            </em>
        </p>

        <p>
            @lang('borrower.loan-application.profile.picture-tip')
        </p>

        <p>
            @lang('borrower.loan-application.profile.picture-example')
        </p>    
    </div>
    
    <div class="col-md-4">
        <img src="{{ \Auth::user()->getProfilePictureUrl() }}" alt=""/>

        <br/>
        <br/>
        
        {{ BootstrapForm::file('picture', ['label' => false]) }}
    </div>
    
    <div class="col-md-8">
        <p>
            <label for="aboutMe" class="control-label">
                @lang('borrower.loan-application.profile.about-me')
            </label>
        </p>
        
        <p>
            <em>
                @lang('borrower.loan-application.profile.about-me-description')
            </em>
        </p>
        
        <p>
            @lang('borrower.loan-application.profile.about-me-example')
        </p>
        
        {{ BootstrapForm::textarea('aboutMe', null, [
            'style' => 'max-width:100%',
            'label' => false,
        ]) }}
        
        <br/>

        <p>
            <label for="aboutBusiness" class="control-label">
                @lang('borrower.loan-application.profile.about-business')
            </label>
        </p>

        <p>
            <em>
                @lang('borrower.loan-application.profile.about-business-description')
            </em>
        </p>

        <p>
            @lang('borrower.loan-application.profile.about-business-tip')
        </p>

        {{ BootstrapForm::textarea('aboutBusiness', null, [
            'style' => 'max-width:100%',
            'label' => false,
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