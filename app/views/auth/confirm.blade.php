@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-offset-3 col-md-6">
        @if(Session::has('error'))
        <div class="alert alert-danger">
            {{ Session::get('error') }}
        </div>
        @endif

        <h2>Complete signup</h2>

        {{ Form::open(array('url' => route('facebook:confirm'))) }}
        <div class="form-group">
            {{ Form::label('username', 'Display Username') }}
            {{ Form::text('username', null, array('class' => 'form-control')) }}
            {{ $errors->first('username', '<div class="alert alert-danger">:message</div>') }}
        </div>
        <div class="form-group">
            {{ Form::label('about_me', 'About Me') }}
            {{ Form::textarea('about_me', null, array('class' => 'form-control')) }}
        </div>

        <button type="submit" class="btn btn-default">Submit</button>

        {{ Form::close() }}
    </div>
</div>
@stop
