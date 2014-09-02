@extends('layouts.master')

@section('page-title')
Dashboard
@stop

@section('content')
<div class="row">
    <div class="col-sm-3 col-md-4">
        <ul class="nav side-menu" role="complementary">
          <h4>Quick Links</h4>
            @include('partials.nav-links.borrower-links')       
          </ul>
    </div>

    <div class="col-sm-9 col-md-8 info-page">
        <div class="page-header">
            <h1>Dashboard</h1>
        </div>

		@if(!$borrower->isVerified())
		    <div class="alert alert-warning">
		        A confirmation message has been sent the email address registered with your Zidisha account. Please click the confirmation link in the message in order to verify your email address.
		        <br/><br/>
				If you did not receive the confirmation message, please click {{ link_to_route('borrower:resend:verification', 'here') }} to resend it.
		    </div>
		@endif

		@if($volunteerMentor)
		If you would like help with Zidisha, you may contact your Volunteer Mentor: <a href="{{ route('page:volunteer-mentor-guidelines') }}">here</a>
		<br>
		<br>
		Name: {{ $volunteerMentor->getName() }}
		<br>
		Telephone: {{ $volunteerMentor->getProfile()->getPhoneNumber() }}
		@endif

		<br><br>
		@include('borrower.dashboard.feedback', compact('feedbackMessages'))
	</div>
</div>
@stop
