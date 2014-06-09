@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-offset-3 col-md-6">
        @if(Session::has('error'))
        <div class="alert alert-danger">
            {{ Session::get('error') }}
        </div>
        @endif

        <div style="text-align: center">
            <a href="{{$facebookJoinUrl}}" class="btn btn-lg btn-primary">Join with facebook</a>
        </div>

        {{ Form::open(array('url' => 'join')) }}
        <div class="form-group">
            {{ Form::label('username', 'Display Username') }}
            {{ Form::text('username', null, array('class' => 'form-control')) }}
            {{ $errors->first('username', '<div class="alert alert-danger">:message</div>') }}
        </div>

        <div class="form-group">
            {{ Form::label('email', 'Email') }}
            {{ Form::text('email', null, array('class' => 'form-control')) }}
            {{ $errors->first('email', '<div class="alert alert-danger">:message</div>') }}
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

        <button type="submit" class="btn btn-default">Submit</button>

        {{ Form::close() }}

        <div>
            {{ link_to_route('login', 'login' ) }}
        </div>

    </div>
</div>
@stop
