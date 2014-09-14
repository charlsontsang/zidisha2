@extends('layouts.master')

@section('content')
<div class="page-header">
    <h1>
        @lang('borrower.join.form.title')
    </h1>
</div>

<div class="row">
    <div class="col-sm-6">
        {{ BootstrapForm::open(['controller' => 'BorrowerJoinController@postCountry', 'translationDomain' => 'borrower.join.form']) }}

        {{ BootstrapForm::select('country', $form->getCountries()->toKeyValue('id', 'name'), ['id' => $country['id'],
        'name' =>$country['name']]) }}

        {{ BootstrapForm::submit('next') }}

        {{ BootstrapForm::close() }}
    </div>
    <div class="col-sm-6">
        {{ BootstrapForm::open(['route' => 'borrower:post:resumeApplication', 'translationDomain' => 'borrower.join.form']) }}
        {{ BootstrapForm::text('resumeCode') }}

        {{ BootstrapForm::submit('resume-submit') }}

        {{ BootstrapForm::close() }}
    </div>
</div>
@stop
