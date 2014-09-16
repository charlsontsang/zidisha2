@extends('layouts.side-menu')

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

<h4>Account Information</h4>

{{ BootstrapForm::text('username', null, ['label' => 'Change Username']) }}

{{ BootstrapForm::text('email', null, ['label' => 'Email']) }}

{{ BootstrapForm::password('password', ['label' => 'Change Password']) }}

{{ BootstrapForm::password('password_confirmation', ['label' => 'Confirm New Password']) }}

<h4>Public Profile</h4>

<p>Introduce yourself to our entrepreneurs!</p>

{{ BootstrapForm::file('picture', ['label' => 'Your Picture']) }}

{{ BootstrapForm::text('city', null, ['label' => 'Your City']) }}

{{ BootstrapForm::textarea('aboutMe', null, ['label' => 'About Yourself']) }}

{{ BootstrapForm::submit('Save') }}

{{ BootstrapForm::close() }}
@stop
