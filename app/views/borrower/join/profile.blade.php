@extends('layouts.master')

@section('content')
{{ BootstrapForm::open(array('controller' => 'BorrowerJoinController@postProfile', 'translationDomain' => 'borrowerJoin.form')) }}
{{ BootstrapForm::populate($form) }}

<p>CREATE ACCOUNT</p>
{{ BootstrapForm::text('username') }}
{{ BootstrapForm::password('password') }}
{{ BootstrapForm::text('email') }}

<br><br>
<p>CONTACT INFORMATION</p>
{{ BootstrapForm::text('firstName') }}
{{ BootstrapForm::text('lastName') }}
{{ BootstrapForm::text('address') }}
<br>
{{ BootstrapForm::label(\Lang::get('borrowerJoin.form.addressInstructions')) }}
<br><br>
{{ BootstrapForm::textArea('addressInstruction') }}
{{ BootstrapForm::text('city') }}
{{ BootstrapForm::text('nationalIdNumber') }}
{{ BootstrapForm::text('phoneNumber', null, ['prepend' => $form->getDialingCode()]) }}
{{ BootstrapForm::text('alternatePhoneNumber', null, [
    'prepend' => $form->getDialingCode(),
    'description' => \Lang::get('borrowerJoin.form.optional')
]) }}

<br><br>
<p>REFERENCES</p>
{{ BootstrapForm::select('members', $form->getBorrowersByCountry()) }}
{{ BootstrapForm::select('volunteer_mentor_city', $form->getVolunteerMentorCity()) }}
{{ BootstrapForm::select('volunteer_mentor') }}

<br><br>
<label>@lang('borrowerJoin.form.communityLeaderDescription')</label>

<fieldset>
    <legend>@lang('borrowerJoin.form.communityLeader')</legend>

    {{ BootstrapForm::text('communityLeader_firstName', null, ['label' => 'borrowerJoin.form.contact.firstName']) }}
    {{ BootstrapForm::text('communityLeader_lastName', null, ['label' => 'borrowerJoin.form.contact.lastName']) }}
    {{ BootstrapForm::text('communityLeader_phoneNumber', null, ['label' => 'borrowerJoin.form.contact.phoneNumber', 'prepend' => $form->getDialingCode()]) }}
    {{ BootstrapForm::text('communityLeader_description', null, ['label' => 'borrowerJoin.form.contact.organizationTitle']) }}
</fieldset>

<label>@lang('borrowerJoin.form.familyMemberDescription')</label>

<fieldset>
    <legend>@lang('borrowerJoin.form.familyMember') 1</legend>

    {{ BootstrapForm::text('familyMember_1_firstName', null, ['label' => 'borrowerJoin.form.contact.firstName']) }}
    {{ BootstrapForm::text('familyMember_1_lastName', null, ['label' => 'borrowerJoin.form.contact.lastName']) }}
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

<label>@lang('borrowerJoin.form.neighborDescription')</label>

<fieldset>
    <legend>@lang('borrowerJoin.form.neighbor') 1</legend>

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

{{ BootstrapForm::submit('submit') }} -
{{ BootstrapForm::submit('save_later') }} -
{{ BootstrapForm::submit('diconnect_facebook_account') }}


{{ BootstrapForm::close() }}
<br/>
<br/>
{{ link_to_route('lender:join', 'Join as lender') }}
@stop


@section('script-footer')
<script type="text/javascript">
    $(function () {
        $("[name=volunteer_mentor_city]").change(function () {
            var $volunteerMentors = $("[name=volunteer_mentor]");
            $.get("{{ route('borrower:join-city', '') }}/" + $(this).val(), function(res) {
                $volunteerMentors.empty();
                $.each(res, function(borrowerId, name) {
                   $volunteerMentors.append('<option value="' + borrowerId + '">' + name + "</option>");
                });
            });

        });
    });
</script>
@stop
