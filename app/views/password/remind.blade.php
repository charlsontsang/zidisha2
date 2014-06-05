@extends('layouts.master')

@section('content')
<div class="row">
    <div style="margin-top: 40px;"></div>

    <div class="col-md-offset-3 col-md-6">
        @if(Session::has('error'))
            <div class="alert alert-danger">
                {{ Session::get('error') }}
            </div>
        @elseif(Session::has('status'))
            <div class="alert alert-info">
                {{ Session::has('status')  }}
            </div>
        @endif
    </div>
    <div class="col-md-offset-3 col-md-6">
        {{ Form::open(array('url' => 'password/remind')) }}
        <div class="form-group">
            {{ Form::label('username', 'Enter your email or username.') }}
            {{ Form::text('username', null, array('class' => 'form-control')) }}
        </div>
        <button type="submit" class="btn btn-default">Submit</button>

        {{ Form::close() }}
    </div>
</div>
@stop
