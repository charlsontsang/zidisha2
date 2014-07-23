@extends('layouts.master')

@section('page-title')
Join the global P2P microlending movement
@stop

@section('content')
<div class="page-header">
    <h1>Edit Profile</h1>
</div>


{{ BootstrapForm::open(array('route' => 'lender:post-profile', 'translationDomain' => 'edit-profile', 'files' => true)) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::file('picture') }}

{{ BootstrapForm::text('username') }}

{{ BootstrapForm::password('password') }}

{{ BootstrapForm::password('password_confirmation') }}

{{ BootstrapForm::text('firstName') }}

{{ BootstrapForm::text('lastName') }}

{{ BootstrapForm::text('email') }}

{{ BootstrapForm::text('city') }}

{{ BootstrapForm::textarea('aboutMe') }}

{{ BootstrapForm::submit('save') }}

{{ BootstrapForm::close() }}

@stop
