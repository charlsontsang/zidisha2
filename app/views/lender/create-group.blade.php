@extends('layouts.master')

@section('page-title')
Create Group
@stop

@section('content')
<h2>Start a new Lending Group</h2>

{{ BootstrapForm::open(array('route' => 'lender:groups:post-create', 'translationDomain' => 'lender.group', 'files' => true)) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::text('name') }}

{{ BootstrapForm::text('website') }}

{{ BootstrapForm::file('profile_picture_id') }}

{{ BootstrapForm::textarea('about') }}

{{ BootstrapForm::submit('save') }}

{{ BootstrapForm::close() }}

@stop
