@extends('layouts.master')

@section('page-title')
Join the global P2P microlending movement
@stop

@section('content')
<div class="page-header">
    <h1>Edit Profile</h1>
</div>


{{ BootstrapForm::open(array('route' => 'borrower:post-profile', 'translationDomain' => 'borrower.edit-profile')) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::text('username') }}

{{ BootstrapForm::password('password') }}

{{ BootstrapForm::password('password_confirmation') }}

{{ BootstrapForm::text('firstName') }}

{{ BootstrapForm::text('lastName') }}

{{ BootstrapForm::text('email') }}

{{ BootstrapForm::textarea('aboutMe') }}

{{ BootstrapForm::textarea('aboutBusiness') }}

{{ BootstrapForm::submit('save') }}

{{ BootstrapForm::close() }}

@stop
