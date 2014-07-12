@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-offset-3 col-md-6">
        <div style="text-align: center">
            <a href="{{$facebookLoginUrl}}" class="btn btn-lg btn-primary">
                @lang('login.facebook_login')
            </a>
        </div>
        <br>
        <div style="text-align: center">
            <a href="{{$googleLoginUrl}}" class="btn btn-lg btn-primary">
                @lang('login.google_login')
            </a>
        </div>

        {{ BootstrapForm::open(array('action' => 'AuthController@postLogin', 'translationDomain' => 'login.form')) }}
        
        {{ BootstrapForm::text('username') }}
        {{ BootstrapForm::password('password') }}

        {{ BootstrapForm::checkbox('remember_me', 'remember_me', false) }}

        {{ BootstrapForm::submit('submit') }}

        {{ BootstrapForm::close() }}

        <div>
            {{ link_to('/password/remind', 'Forgot Password?' ) }}
            <br/>
            {{ link_to_route('join', 'Join' ) }}
        </div>
    </div>
</div>
@stop
