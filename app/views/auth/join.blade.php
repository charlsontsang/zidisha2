@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-offset-3 col-md-6">
        <div style="text-align: center">
            <a href="{{$facebookJoinUrl}}" class="btn btn-lg btn-primary">Join with facebook</a>
        </div>

        {{ BootstrapForm::open(array('url' => 'join', 'translationDomain' => 'join.form')) }}
        
        {{ BootstrapForm::text('username') }}
        {{ BootstrapForm::text('email') }}
        {{ BootstrapForm::password('password') }}
        {{ BootstrapForm::password('password_confirmation') }}
        {{ BootstrapForm::submit('submit') }}

        {{ BootstrapForm::close() }}

        <div>
            {{ link_to_route('login', 'login' ) }}
        </div>

    </div>
</div>
@stop
