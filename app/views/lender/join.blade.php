@extends('layouts.master')

@section('content')
{{ link_to_route('borrower:join', 'Join as borrower') }}
<br/>
<br/>
<div class="row">
    <div class="col-md-offset-3 col-md-6">
        <div style="text-align: center">
            <a href="{{$facebookJoinUrl}}" class="btn btn-lg btn-primary">Join with Facebook</a>
        </div>
        <br>
        <div style="text-align: center">
            <a href="{{$googleLoginUrl}}" class="btn btn-lg btn-primary">
                Join with Google
            </a>
        </div>

        {{ BootstrapForm::open(array('route' => 'lender:post-join', 'translationDomain' => 'lender.join.form')) }}
        {{ BootstrapForm::populate($form) }}

        {{ BootstrapForm::text('username') }}
        {{ BootstrapForm::text('email') }}
        {{ BootstrapForm::password('password') }}
        {{ BootstrapForm::password('password_confirmation') }}
        {{ BootstrapForm::select(
            'countryId',
            $form->getCountries()->toKeyValue('id', 'name'),
            [
                'id'   => $country['id'],
                'name' => $country['name']
            ]
        ) }}
        {{ BootstrapForm::submit('submit') }}

        {{ BootstrapForm::close() }}

        <div>
            {{ link_to_route('login', 'login' ) }}
        </div>

    </div>
</div>
@stop
