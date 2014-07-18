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

        {{ BootstrapForm::open([
               'route' => 'lender:post-join',
               'translationDomain' => 'lender.join.form',
               'id' => 'joinForm']
        ) }}
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
        {{ BootstrapForm::checkbox('termsOfUse') }}
        I have read and agree to the <a href="#" data-toggle="modal" data-target="#termsOfUseModal">Zidisha Terms of Use</a>
        and <a target="_blank" href="http://www.iubenda.com/privacy-policy/629677/legal">Privacy Policy</a>
        {{ BootstrapForm::submit('submit') }}

        {{ BootstrapForm::close() }}

        <div>
            {{ link_to_route('login', 'login' ) }}
        </div>

    </div>
</div>

@include('partials._modal', [
    'title' => 'Terms of use',
    'template' => 'lender.terms-of-use',
    'id' => 'termsOfUseModal',
    'scrollable' => true
])
@stop

@section('script-footer')
<script type="text/javascript">
$(function() {
    $('#joinForm').submit(function() {
        if (!$('[name=termsOfUse]').is(':checked')) {
            alert('ljhglj');
            return false;
        }
    });
});
</script>

@stop
