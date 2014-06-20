@extends('layouts.master')

@section('content')
{{ BootstrapForm::open(array('controller' => 'BorrowerJoinController@postProfile', 'translationDomain' => 'borrower.join.profile')) }}

{{ BootstrapForm::populate($form) }}
<br><br>
<p>CREATE ACCOUNT</p>
{{ BootstrapForm::text('username', null , ['label' => \Lang::get('join.borrower.form.username')]) }}
{{ BootstrapForm::password('password', ['label' => \Lang::get('join.borrower.form.password')]) }}
{{ BootstrapForm::text('email', null , ['label' => \Lang::get('join.borrower.form.email')]) }}

<br><br>
<p>CONTACT INFORMATION</p>
{{ BootstrapForm::text('first_name', null , ['label' => \Lang::get('join.borrower.form.first_name')]) }}
{{ BootstrapForm::text('last_name', null , ['label' => \Lang::get('join.borrower.form.last_name')]) }}
{{ BootstrapForm::text('address', null , ['label' => \Lang::get('join.borrower.form.address')]) }}
<br>
{{ BootstrapForm::label(\Lang::get('join.borrower.form.address_instruction_1')) }}
<br><br>
{{ BootstrapForm::textArea('address_instruction', null , ['label' => \Lang::get('join.borrower.form.address_instruction_2')]) }}
{{ BootstrapForm::text('village', null , ['label' => \Lang::get('join.borrower.form.village')]) }}
{{ BootstrapForm::text('national_id_number', null , ['label' => \Lang::get('join.borrower.form.national_id_number')]) }}
{{ BootstrapForm::text('phone_number', null , ['label' => \Lang::get('join.borrower.form.phone_number')]) }}
{{ BootstrapForm::label(\Lang::get('join.borrower.form.optional')) }}
{{ BootstrapForm::text('alternate_phone_number', null , ['label' => \Lang::get('join.borrower.form.alternate_phone_number')]) }}

<br><br>
<p>REFERENCES</p>
{{ BootstrapForm::select('members', [], null, ['label' => \Lang::get('join.borrower.form.members')]) }}
{{ BootstrapForm::select('town', [], null, ['label' => \Lang::get('join.borrower.form.town')]) }}
{{ BootstrapForm::select('mentor', [], null, ['label' => \Lang::get('join.borrower.form.mentor')]) }}

<br><br>
<p>Please enter the contact information of a community leader, such as the leader of a local school, religious
    institution or other community organization, who knows you well and can recommend you for a Zidisha loan.</p>

{{ BootstrapForm::submit('submit') }} -
{{ BootstrapForm::submit('save_later') }} -
{{ BootstrapForm::submit('diconnect_facebook_account') }}


{{ BootstrapForm::close() }}
<br/>
<br/>
{{ link_to_route('lender:join', 'Join as lender') }}
@stop
