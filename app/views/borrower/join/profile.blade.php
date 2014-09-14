@extends('layouts.master')

@section('content')
<div class="page-header">
    <h1>
        @lang('borrower.join.form.title')
    </h1>
</div>

{{ BootstrapForm::open(array('controller' => 'BorrowerJoinController@postProfile', 'translationDomain' => 'borrower.join.form', 'id' => 'borrowerRegistrationForm')) }}
{{ BootstrapForm::populate($form) }}

<p>
    <button type="submit" class="btn btn-facebook" name="disconnect-facebook" value="disconnect-facebook">
        <span class="fa fa-facebook fa-lg fa-fw"></span>
        @lang('borrower.join.form.disconnect-facebook')
    </button>
</p>

<fieldset>
    <legend>
        @lang('borrower.join.form.create-account')
    </legend>

    {{ BootstrapForm::text('username') }}
    {{ BootstrapForm::password('password') }}
    {{ BootstrapForm::text('email') }}
</fieldset>

<fieldset>
    <legend>
        @lang('borrower.join.form.more-info')
    </legend>

    {{ BootstrapForm::text('preferredLoanAmount', null, ['append' => $form->getCountry()->getCurrencyCode()]) }}
    {{ BootstrapForm::text('preferredInterestRate', null, ['append' => '%']) }}
    {{ BootstrapForm::text('preferredRepaymentAmount') }}
    {{ BootstrapForm::select('businessCategoryId', $form->getCategories()) }}
    {{ BootstrapForm::select('businessYears', $form->getBusinessYears()) }}
    {{ BootstrapForm::select('loanUsage', $form->getLoanUsage()) }}
    {{ BootstrapForm::datepicker('birthDate') }}
</fieldset>

<fieldset>
    <legend>
        @lang('borrower.join.form.contact-info')
    </legend>

    {{ BootstrapForm::text('firstName') }}
    {{ BootstrapForm::text('lastName') }}
    {{ BootstrapForm::text('address') }}

    {{ BootstrapForm::textArea('addressInstructions') }}
    {{ BootstrapForm::text('city') }}
    {{ BootstrapForm::text('nationalIdNumber') }}
    {{ BootstrapForm::text('phoneNumber', null, ['prepend' => $form->getDialingCode()]) }}
    {{ BootstrapForm::text('alternatePhoneNumber', null, [
        'prepend' => $form->getDialingCode(),
        'description' => \Lang::get('borrower.join.form.alternate-phone-number-description')
    ]) }}
</fieldset>

<fielset>
    <legend>
        @lang('borrower.join.form.references')
    </legend>

    {{ BootstrapForm::select('referrerId', $form->getBorrowersByCountry()) }}
    {{ BootstrapForm::select('volunteerMentorCity', $form->getVolunteerMentorCities()) }}
    {{ BootstrapForm::select('volunteerMentorId', $form->getVolunteerMentors()) }}
</fielset>

<p class="well">
    @lang('borrower.join.form.community-leader-description')
</p>

<fieldset>
    <legend>@lang('borrower.join.form.community-leader')</legend>

    {{ BootstrapForm::text('communityLeader_firstName', null, ['label' => 'borrower.join.form.contact.first-name']) }}
    {{ BootstrapForm::text('communityLeader_lastName', null, ['label' => 'borrower.join.form.contact.last-name']) }}
    {{ BootstrapForm::text('communityLeader_phoneNumber', null, ['label' => 'borrower.join.form.contact.phone-number', 'prepend' => $form->getDialingCode()]) }}
    {{ BootstrapForm::text('communityLeader_description', null, ['label' => 'borrower.join.form.contact.organization-title']) }}
</fieldset>

<p class="well">
    @lang('borrower.join.form.family-member-description')
</p>

<fieldset>
    <legend>@lang('borrower.join.form.family-member') 1</legend>

    {{ BootstrapForm::text('familyMember_1_firstName', null, ['label' => 'borrower.join.form.contact.first-name']) }}
    {{ BootstrapForm::text('familyMember_1_lastName', null, ['label' => 'borrower.join.form.contact.last-name']) }}
    {{ BootstrapForm::text('familyMember_1_phoneNumber', null, ['label' => 'borrower.join.form.contact.phone-number', 'prepend' => $form->getDialingCode()]) }}
    {{ BootstrapForm::text('familyMember_1_description', null, ['label' => 'borrower.join.form.contact.relationship']) }}
</fieldset>

<fieldset>
    <legend>@lang('borrower.join.form.family-member') 2</legend>

    {{ BootstrapForm::text('familyMember_2_firstName', null, ['label' => 'borrower.join.form.contact.first-name']) }}
    {{ BootstrapForm::text('familyMember_2_lastName', null, ['label' => 'borrower.join.form.contact.last-name']) }}
    {{ BootstrapForm::text('familyMember_2_phoneNumber', null, ['label' => 'borrower.join.form.contact.phone-number', 'prepend' => $form->getDialingCode()]) }}
    {{ BootstrapForm::text('familyMember_2_description', null, ['label' => 'borrower.join.form.contact.relationship']) }}
</fieldset>

<fieldset>
    <legend>@lang('borrower.join.form.family-member') 3</legend>

    {{ BootstrapForm::text('familyMember_3_firstName', null, ['label' => 'borrower.join.form.contact.first-name']) }}
    {{ BootstrapForm::text('familyMember_3_lastName', null, ['label' => 'borrower.join.form.contact.last-name']) }}
    {{ BootstrapForm::text('familyMember_3_phoneNumber', null, ['label' => 'borrower.join.form.contact.phone-number', 'prepend' => $form->getDialingCode()]) }}
    {{ BootstrapForm::text('familyMember_3_description', null, ['label' => 'borrower.join.form.contact.relationship']) }}
</fieldset>

<label>@lang('borrower.join.form.neighbor-description')</label>

<fieldset>
    <legend>@lang('borrower.join.form.neighbor') 1</legend>

    {{ BootstrapForm::text('neighbor_1_firstName', null, ['label' => 'borrower.join.form.contact.first-name']) }}
    {{ BootstrapForm::text('neighbor_1_lastName', null, ['label' => 'borrower.join.form.contact.last-name']) }}
    {{ BootstrapForm::text('neighbor_1_phoneNumber', null, ['label' => 'borrower.join.form.contact.phone-number', 'prepend' => $form->getDialingCode()]) }}
    {{ BootstrapForm::text('neighbor_1_description', null, ['label' => 'borrower.join.form.contact.relationship']) }}
</fieldset>

<fieldset>
    <legend>@lang('borrower.join.form.neighbor') 2</legend>

    {{ BootstrapForm::text('neighbor_2_firstName', null, ['label' => 'borrower.join.form.contact.first-name']) }}
    {{ BootstrapForm::text('neighbor_2_lastName', null, ['label' => 'borrower.join.form.contact.last-name']) }}
    {{ BootstrapForm::text('neighbor_2_phoneNumber', null, ['label' => 'borrower.join.form.contact.phone-number', 'prepend' => $form->getDialingCode()]) }}
    {{ BootstrapForm::text('neighbor_2_description', null, ['label' => 'borrower.join.form.contact.relationship']) }}
</fieldset>

<fieldset>
    <legend>@lang('borrower.join.form.neighbor') 3</legend>

    {{ BootstrapForm::text('neighbor_3_firstName', null, ['label' => 'borrower.join.form.contact.first-name']) }}
    {{ BootstrapForm::text('neighbor_3_lastName', null, ['label' => 'borrower.join.form.contact.last-name']) }}
    {{ BootstrapForm::text('neighbor_3_phoneNumber', null, ['label' => 'borrower.join.form.contact.phone-number', 'prepend' => $form->getDialingCode()]) }}
    {{ BootstrapForm::text('neighbor_3_description', null, ['label' => 'borrower.join.form.contact.relationship']) }}
</fieldset>

<fieldset>
    <legend>
        @lang('borrower.join.form.terms-and-condition.legend')
    </legend>
</fieldset>

<div class="checkbox">
    <label>
        <input id="termsAndConditionCheckbox" name="termsAndCondition" type="checkbox">
            {{ \Lang::get('borrower.join.form.terms-and-condition.confirmation') }}
            <a data-toggle="modal" data-target="#termsAndConditionModal">
                {{ \Lang::get('borrower.join.form.terms-and-condition.link') }}
            </a>
    </label>
</div>

<br/>
<br/>

{{ BootstrapForm::submit('submit') }}
{{ BootstrapForm::submit('save-later', ['class' => 'btn btn-default']) }}

{{ BootstrapForm::close() }}

@include('partials._modal', [
    'id' => 'termsAndConditionModal',
    'title' => \Lang::get('borrower.join.form.terms-and-condition.title'),
    'body' => \Lang::get('borrower.join.form.terms-and-condition.body')
])
@stop

@section('script-footer')
<script type="text/javascript">
    $(function () {
        $("[name=volunteerMentorCity]").change(function () {
            var $volunteerMentors = $("[name=volunteerMentorId]");
            $volunteerMentors.empty();
            $.get("{{ route('borrower:join-city', '') }}/" + $(this).val(), function(res) {
                $.each(res, function(borrowerId, name) {
                   $volunteerMentors.append('<option value="' + borrowerId + '">' + name + "</option>");
                });
            });
        });

        $('[name=submit], [name=save-later]').click(function() {
            if (!$('#termsAndConditionCheckbox').is(":checked")){
                alert("@lang('borrower.join.form.terms-and-condition.please-agree')");
                return false;
            }
        });

        $('input').on('keyup keypress', function(e) {
            if (e.which  == 13) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
@stop
