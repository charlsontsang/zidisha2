@extends('layouts.master')

@section('content')
{{ BootstrapForm::open(array('controller' => 'BorrowerJoinController@postProfile', 'translationDomain' => 'borrower.join.profile')) }}

{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::text('first_name') }}
{{ BootstrapForm::text('last_name') }}
{{ BootstrapForm::text('email') }}

{{ BootstrapForm::submit('submit') }} -
{{ BootstrapForm::submit('save_later') }} -
{{ BootstrapForm::submit('diconnect_facebook_account') }}


{{ BootstrapForm::close() }}
<br/>
<br/>
{{ link_to_route('lender:join', 'Join as lender') }}
@stop
