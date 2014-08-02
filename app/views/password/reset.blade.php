@extends('layouts.master')

@section('content')
<div class="row">
    <div style="margin-top: 40px;"></div>

    <div class="col-md-offset-3 col-md-6">
        <div class="page-header">
            <h1>Reset your password</h1>
        </div>
        {{ Form::open(array('url' => 'password/reset')) }}

        <div class="form-group">
            {{ Form::label('username', \Lang::get('borrower.reminders.username-or-password')) }}
            {{ Form::text('username', null, array('class' => 'form-control')) }}
            {{ $errors->first('username', '<div class="alert alert-danger">:message</div>') }}
        </div>

        <div class="form-group">
            {{ Form::label('password', \Lang::get('borrower.reminders.create-password')) }}
            {{ Form::password('password', array('class' => 'form-control')) }}
            {{ $errors->first('password',  '<div class="alert alert-danger">:message</div>') }}
        </div>

        <div class="form-group">
            {{ Form::label('password_confirmation', 'Confirm Password') }}
            {{ Form::password('password_confirmation', array('class' => 'form-control')) }}
        </div>

        {{ Form::hidden('token', $token) }}

        <button type="submit" class="btn btn-default">Submit</button>

        {{ Form::close() }}
    </div>
</div>
@stop
