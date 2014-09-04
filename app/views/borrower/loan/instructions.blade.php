@extends('layouts.master')

@section('page-title')
    @lang("borrower.loan-application.progress-bar.instructions-page")
@stop

@section('content')

@include('borrower.loan.partials.application-steps')

<div class="page-header">
    <h1>@lang("borrower.loan-application.title.instructions-page")</h1>
</div>

<p>
    @lang("borrower.loan-application.instructions.intro")
</p>
<p>
    @lang("borrower.loan-application.instructions.deadline")
</p>

<p>
    @lang("borrower.loan-application.instructions.tips")
</p>

<ol>
    <li>
        @lang("borrower.loan-application.instructions.tip1")
    </li>
    
    <li>
        @lang("borrower.loan-application.instructions.tip2")
    </li>

    <li>
        @lang("borrower.loan-application.instructions.tip3")
    </li>
</ol>

<p>
    @lang("borrower.loan-application.instructions.more-tips", ['link' => route('page:loan-feature-criteria')])
</p>

<div class="clearfix">
    <div class="pull-right">
        {{ BootstrapForm::open(array('controller' => 'LoanApplicationController@postInstructions')) }}

        {{ BootstrapForm::submit(Lang::get('borrower.loan-application.next') . ': ' . Lang::get('borrower.loan-application.title.profile-page')) }}

        {{ BootstrapForm::close() }}
    </div>
</div>
@stop
