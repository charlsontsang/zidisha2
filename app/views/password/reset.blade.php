@extends('layouts.master')

@section('content')
<div class="row">
    <div style="margin-top: 40px;"></div>

    @if(Session::has('error'))
    <div class="alert alert-danger">
        {{ Session::get('error')}}
    </div>
    @endif
    <div class="col-md-offset-3 col-md-6">
        {{ Form::open(array('url' => 'password/reset')) }}

        <div class="form-group">
            {{ Form::label('username', 'Username or email') }}
            {{ Form::text('username', null, array('class' => 'form-control')) }}
            {{ $errors->first('username', '<div class="alert alert-danger">:message</div>') }}
        </div>

        <div class="form-group">
            {{ Form::label('password', 'Password') }}
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
