@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <h1 class="page-title">
            @lang('borrower.join.form.title')
        </h1>
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    @lang('borrower.join.form.start-new')
                </h3>
            </div>
            <div class="panel-body">

                {{ BootstrapForm::open(['controller' => 'BorrowerJoinController@postCountry', 'translationDomain' => 'borrower.join.form']) }}

                {{ BootstrapForm::select('country', $form->getCountries()->toKeyValue('id', 'name'), ['id' => $country['id'],
                'name' =>$country['name']]) }}

                {{ BootstrapForm::submit('next') }}

                {{ BootstrapForm::close() }}

            </div>
        </div>
        
        <br/><br/>
        
        <p>
            {{ BootstrapForm::open(['route' => 'borrower:post:resumeApplication', 'translationDomain' => 'borrower.join.form']) }}
            {{ BootstrapForm::text('resumeCode') }}

            {{ BootstrapForm::submit('resume-submit', ['class' => 'btn-default']) }}

            {{ BootstrapForm::close() }}
        </p>
    </div>
</div>

@stop
