@extends('layouts.master')

@section('page-title')
Start a new Lending Group
@stop

@section('content')
<h2>Start a new Lending Group</h2>

{{ BootstrapForm::open(array('route' => 'lender:groups:post-create', 'translationDomain' => 'lender.group', 'files' => true)) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::text('name') }}

{{ BootstrapForm::text('website') }}

{{ BootstrapForm::file('groupProfilePictureId') }}

{{ BootstrapForm::textarea('about') }}

{{ BootstrapForm::submit('save') }}

{{ BootstrapForm::close() }}

@stop
