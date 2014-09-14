@extends('layouts.master')

@section('page-title')
Join the global P2P microlending movement
@stop

@section('content')
<div class="page-header">
    <h1>Edit Borrower Profile</h1>
</div>
<p class="pull-right"><a href="{{ route('admin:borrower', $borrower->getId()) }}">View Profile</a></p>

<div class="borrower-personal-information-form">
{{ BootstrapForm::open(array('route' => ['admin:borrower:edit:post', $borrowerId], 'translationDomain' => 'borrower.personal-information')) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::text('firstName') }}

{{ BootstrapForm::text('lastName') }}

{{ BootstrapForm::text('email') }}

{{ BootstrapForm::password('password') }}

<p>CONTACT INFORMATION</p>

{{ BootstrapForm::textarea('address') }}

{{ BootstrapForm::textarea('addressInstructions') }}

{{ BootstrapForm::text('city') }}

{{ BootstrapForm::select('countryId', $form->getBorrowerCountries()) }}


{{ BootstrapForm::text('phoneNumber', null, ['prepend' => $form->getDialingCode()]) }}

{{ BootstrapForm::text('alternatePhoneNumber', null, ['prepend' => $form->getDialingCode(), 'description' => \Lang::get('borrowerJoin.form.optional') ]) }}

<br><br>


<fieldset>
    <legend>@lang('borrowerJoin.form.communityLeader')</legend>
    <p>@lang('borrowerJoin.form.communityLeaderDescription')</p>

    {{ BootstrapForm::text('communityLeader_firstName', null, ['label' => 'borrowerJoin.form.contact.firstName']) }}

    {{ BootstrapForm::text('communityLeader_lastName', null, ['label' => 'borrowerJoin.form.contact.firstName']) }}

    {{ BootstrapForm::text('communityLeader_phoneNumber', null, ['label' => 'borrowerJoin.form.contact.phoneNumber', 'prepend' => $form->getDialingCode()]) }}

    {{ BootstrapForm::text('communityLeader_description', null, ['label' => 'borrowerJoin.form.contact.phoneNumber', 'prepend' => $form->getDialingCode()]) }}

</fieldset>


<fieldset>
    <legend>@lang('borrowerJoin.form.familyMember') 1</legend>
    <p>@lang('borrowerJoin.form.familyMemberDescription')</p>

    {{ BootstrapForm::text('familyMember_1_firstName', null, ['label' => 'borrowerJoin.form.contact.firstName']) }}

    {{ BootstrapForm::text('familyMember_1_lastName', null, ['label' => 'borrowerJoin.form.contact.firstName']) }}

    {{ BootstrapForm::text('familyMember_1_phoneNumber', null, ['label' => 'borrowerJoin.form.contact.phoneNumber', 'prepend' => $form->getDialingCode()]) }}

    {{ BootstrapForm::text('familyMember_1_description', null, ['label' => 'borrowerJoin.form.contact.relationship']) }}

</fieldset>

<fieldset>
    <legend>@lang('borrowerJoin.form.familyMember') 2</legend>

    {{ BootstrapForm::text('familyMember_2_firstName', null, ['label' => 'borrowerJoin.form.contact.firstName']) }}

    {{ BootstrapForm::text('familyMember_2_lastName', null, ['label' => 'borrowerJoin.form.contact.lastName']) }}

    {{ BootstrapForm::text('familyMember_2_phoneNumber', null, ['label' => 'borrowerJoin.form.contact.phoneNumber', 'prepend' => $form->getDialingCode()]) }}

    {{ BootstrapForm::text('familyMember_2_description', null, ['label' => 'borrowerJoin.form.contact.relationship']) }}
</fieldset>

<fieldset>
    <legend>@lang('borrowerJoin.form.familyMember') 3</legend>

    {{ BootstrapForm::text('familyMember_3_firstName', null, ['label' => 'borrowerJoin.form.contact.firstName']) }}

    {{ BootstrapForm::text('familyMember_3_lastName', null, ['label' => 'borrowerJoin.form.contact.lastName']) }}

    {{ BootstrapForm::text('familyMember_3_phoneNumber', null, ['label' => 'borrowerJoin.form.contact.phoneNumber', 'prepend' => $form->getDialingCode()]) }}

    {{ BootstrapForm::text('familyMember_3_description', null, ['label' => 'borrowerJoin.form.contact.relationship']) }}
</fieldset>


<fieldset>
    <legend>@lang('borrowerJoin.form.neighbor') 1</legend>
    <p>@lang('borrowerJoin.form.neighborDescription')</p>

    {{ BootstrapForm::text('neighbor_1_firstName', null, ['label' => 'borrowerJoin.form.contact.firstName']) }}

    {{ BootstrapForm::text('neighbor_1_lastName', null, ['label' => 'borrowerJoin.form.contact.lastName']) }}

    {{ BootstrapForm::text('neighbor_1_phoneNumber', null, ['label' => 'borrowerJoin.form.contact.phoneNumber', 'prepend' => $form->getDialingCode()]) }}

    {{ BootstrapForm::text('neighbor_1_description', null, ['label' => 'borrowerJoin.form.contact.relationship']) }}

</fieldset>

<fieldset>
    <legend>@lang('borrowerJoin.form.neighbor') 2</legend>

    {{ BootstrapForm::text('neighbor_2_firstName', null, ['label' => 'borrowerJoin.form.contact.firstName']) }}

    {{ BootstrapForm::text('neighbor_2_lastName', null, ['label' => 'borrowerJoin.form.contact.lastName']) }}

    {{ BootstrapForm::text('neighbor_2_phoneNumber', null, ['label' => 'borrowerJoin.form.contact.phoneNumber', 'prepend' => $form->getDialingCode()]) }}

    {{ BootstrapForm::text('neighbor_2_description', null, ['label' => 'borrowerJoin.form.contact.relationship']) }}

</fieldset>

<fieldset>
    <legend>@lang('borrowerJoin.form.neighbor') 3</legend>

    {{ BootstrapForm::text('neighbor_3_firstName', null, ['label' => 'borrowerJoin.form.contact.firstName']) }}

    {{ BootstrapForm::text('neighbor_3_lastName', null, ['label' => 'borrowerJoin.form.contact.lastName']) }}

    {{ BootstrapForm::text('neighbor_3_phoneNumber', null, ['label' => 'borrowerJoin.form.contact.phoneNumber', 'prepend' => $form->getDialingCode()]) }}

    {{ BootstrapForm::text('neighbor_3_description', null, ['label' => 'borrowerJoin.form.contact.relationship']) }}

</fieldset>

{{ BootstrapForm::submit('update') }}
{{ BootstrapForm::close() }}
</div>
@stop
