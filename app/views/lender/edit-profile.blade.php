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

		{{ BootstrapForm::open(array('route' => 'lender:post-profile', 'translationDomain' => 'edit-profile', 'files' => true)) }}
		{{ BootstrapForm::populate($form) }}

		{{ BootstrapForm::file('picture') }}

		{{ BootstrapForm::password('password') }}

		{{ BootstrapForm::password('password_confirmation') }}

		{{ BootstrapForm::text('firstName') }}

		{{ BootstrapForm::text('lastName') }}

		{{ BootstrapForm::text('email') }}

		{{ BootstrapForm::text('city') }}

		{{ BootstrapForm::textarea('aboutMe') }}

		{{ BootstrapForm::submit('save') }}

		{{ BootstrapForm::close() }}
		
	</div>
</div>

@stop
