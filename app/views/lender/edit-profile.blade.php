@extends('layouts.master')

@section('page-title')
Edit Profile
@stop

@section('content')
<div class="row">
    <div class="col-sm-3 col-md-4">
        <ul class="nav side-menu" role="complementary">
          <h4>Quick Links</h4>
          	@include('partials.nav-links.lender-links')       
          </ul>
    </div>

    <div class="col-sm-9 col-md-8 info-page">
        <div class="page-header">
            <h1>Edit Profile</h1>
        </div>

		{{ BootstrapForm::open(array('route' => 'lender:post-profile', 'files' => true)) }}
		{{ BootstrapForm::populate($form) }}

		<h4>Account Information</h4>

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
		
	</div>
</div>

@stop
