@extends('layouts.side-menu-simple')

@section('page-title')
Edit Profile
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.lender-links')
@stop

@section('page-content')

{{ BootstrapForm::open(array('route' => 'lender:post-profile', 'files' => true)) }}
{{ BootstrapForm::populate($form) }}

    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">
                Public Profile
            </h3>
        </div>
        <div class="panel-body">

		<p>Introduce yourself to our entrepreneurs!</p>

		{{ BootstrapForm::file('picture', ['label' => 'Your Picture']) }}

		{{ BootstrapForm::text('firstName', null, ['label' => 'Your FirstName']) }}

		{{ BootstrapForm::text('lastName', null, ['label' => 'Your LastName']) }}

		{{ BootstrapForm::text('city', null, ['label' => 'Your City']) }}

		{{ BootstrapForm::textarea('aboutMe', null, ['label' => 'About Yourself']) }}

        </div>
    </div>
    
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">
                Account Information
            </h3>
        </div>
        <div class="panel-body">

            {{ BootstrapForm::text('username', null, ['label' => 'Change Username']) }}

            {{ BootstrapForm::text('email', null, ['label' => 'Email']) }}

            {{ BootstrapForm::password('password', ['label' => 'Change Password']) }}

            {{ BootstrapForm::password('password_confirmation', ['label' => 'Confirm New Password']) }}

        </div>
    </div>

	{{ BootstrapForm::submit('Save') }}

	{{ BootstrapForm::close() }}
@stop
