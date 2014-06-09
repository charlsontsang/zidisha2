@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-offset-3 col-md-6">
        @if(Session::has('error'))
        <div class="alert alert-danger">
            {{ Session::get('error') }}
        </div>
        @endif

        @if(Session::has('success'))
        <div class="alert alert-success">
            {{ Session::get('success') }}
        </div>
        @endif

        <div style="text-align: center">
            <a href="{{$facebookLoginUrl}}" class="btn btn-lg btn-primary">Login with facebook</a>
        </div>

        {{ Form::open(array('url' => 'login')) }}
        <div class="form-group">
            {{ Form::label('username', 'Display Username') }}
            {{ Form::text('username', null, array('class' => 'form-control')) }}
        </div>

        <div class="form-group">
            {{ Form::label('password', 'Password') }}
            {{ Form::password('password', array('class' => 'form-control')) }}
        </div>

        <div class="form-group">
            <label>
                {{ Form::checkbox('remember_me', 'remember_me', false) }} Remember me
            </label>
        </div>

        <button type="submit" class="btn btn-default">Submit</button>

        {{ Form::close() }}

        <div>
            {{ link_to('/password/remind', 'Forgot Password?' ) }}
            <br/>
            {{ link_to_route('join', 'Join' ) }}
        </div>
    </div>
</div>
@stop
