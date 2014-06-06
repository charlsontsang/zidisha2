@extends('layouts.master')

@section('page-title')
Join the global P2P microlending movement
@stop

@section('content')
<div class="page-header">
    <h1>Edit Profile</h1>
</div>

@if($lender)
{{ Form::open(array('route' => 'lender:post-profile')) }}
<p>
    {{ Form::label('username', 'Create Display Username ') }}
    {{ Form::text('username', Auth::user()->getUsername()) }}
    {{ $errors->first('username') }}
</p>
<p>
    {{ Form::label('password', 'Change Password ') }}
    {{ Form::password('password') }}
    {{ $errors->first('password') }}
</p>
<p>
    {{ Form::label('password_confirmation', 'Confirm Password ') }}
    {{ Form::password('password_confirmation') }}
</p>
<p>
    {{ Form::label('firstName', 'First Name ') }}
    {{ Form::text('firstName', $lender->getFirstName()) }}
    {{ $errors->first('firstName') }}
</p>
<p>
    {{ Form::label('lastName', 'Last Name ') }}
    {{ Form::text('lastName', $lender->getLastName()) }}
    {{ $errors->first('lastName') }}
</p>
<p>
    {{ Form::label('email', 'Email Address ') }}
    {{ Form::text('email', Auth::user()->getEmail()) }}
    {{ $errors->first('email') }}
</p>
<p>
    {{ Form::label('aboutMe', 'About Me ') }}
    {{ Form::textarea('aboutMe', $lender->getAboutMe()) }}
    {{ $errors->first('aboutMe') }}
</p>
{{ Form::submit('Save Changes') }}
{{ Form::close() }}
@else
<p>Wrong Username!</p>
@endif


@stop
