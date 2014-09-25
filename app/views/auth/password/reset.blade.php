@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-offset-3 col-md-6">
        <div class="highlight highlight-panel">
            <div class="page-header">
                <h1>Reset your password</h1>
            </div>
            {{ Form::open(array('url' => 'password/reset')) }}

            <div class="form-group">
                {{ Form::label('email', \Lang::get('borrower.reminders.email-password-reset')) }}
                {{ Form::text('email', null, array('class' => 'form-control')) }}
                {{ $errors->first('email', '<div class="alert alert-danger">:message</div>') }}
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
</div>
@stop
