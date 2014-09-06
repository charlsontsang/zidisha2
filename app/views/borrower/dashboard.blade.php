@extends('layouts.side-menu')

@section('page-title')
Dashboard
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
    @include('partials.nav-links.borrower-links')
@stop


@section('page-content')

@include('borrower.dashboard.partials.'.$partial)
        
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
Name: <a href="{{ route('borrower:public-profile', $borrower->getId()) }}">{{ $volunteerMentor->getName() }}</a>
<br>
Telephone: {{ $volunteerMentor->getProfile()->getPhoneNumber() }}
@endif

<br><br>
@include('borrower.dashboard.feedback', compact('feedbackMessages'))
@stop
