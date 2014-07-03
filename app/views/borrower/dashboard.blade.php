@extends('layouts.master')

@section('page-title')
Dashboard
@stop

@section('content')
<div class="page-header">
    <h1>Dashboard</h1>
</div>

@if(!$verified)
    <div class="alert alert-warning">
        Your email is not verified. Please verify your email. Click {{ link_to_route('borrower:resend:verification', 'here') }} to resend your verification mail.
    </div>
@endif

<h2>Dashboard for borrowers</h2>

@if($volunteerMentor)
If you like help with Zidisha, You may contact your Volunteer mentor: <a href="{{ route('page:volunteer-mentor-guidelines') }}">here</a>
<br>
<br>
Name: {{ $volunteerMentor->getName() }}
<br>
Telephone: {{ $volunteerMentor->getProfile()->getPhoneNumber() }}
@endif

@stop
