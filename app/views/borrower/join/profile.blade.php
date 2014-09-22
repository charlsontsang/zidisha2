@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <h1 class="page-title">
            @lang('borrower.join.form.title')
        </h1>

        {{ BootstrapForm::open(array('controller' => 'BorrowerJoinController@postProfile', 'translationDomain' => 'borrower.join.form', 'id' => 'borrowerRegistrationForm')) }}
        {{ BootstrapForm::populate($form) }}

        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    @lang('borrower.join.form.create-account')
                </h3>
            </div>
            <div class="panel-body">

                {{ BootstrapForm::text('username') }}
                {{ BootstrapForm::password('password') }}
                {{ BootstrapForm::text('email') }}

            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    @lang('borrower.join.form.contact-info')
                </h3>
            </div>
            <div class="panel-body">

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
    
            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    @lang('borrower.join.form.mentor-title')
                </h3>
            </div>
            <div class="panel-body">

                {{ BootstrapForm::select('volunteerMentorCity', $form->getVolunteerMentorCities()) }}
                {{ BootstrapForm::select('volunteerMentorId', $form->getVolunteerMentors()) }}
            
            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    @lang('borrower.join.form.cl-title')
                </h3>
            </div>
            <div class="panel-body">
                <p>
                    @lang('borrower.join.form.community-leader-description')
                </p>

                {{ BootstrapForm::text('communityLeader_firstName', null, ['label' => 'borrower.join.form.contact.first-name']) }}
                {{ BootstrapForm::text('communityLeader_lastName', null, ['label' => 'borrower.join.form.contact.last-name']) }}
                {{ BootstrapForm::text('communityLeader_phoneNumber', null, ['label' => 'borrower.join.form.contact.phone-number', 'prepend' => $form->getDialingCode()]) }}
                {{ BootstrapForm::text('communityLeader_description', null, ['label' => 'borrower.join.form.contact.organization-title']) }}
            
            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    @lang('borrower.join.form.family-title')
                </h3>
            </div>
            <div class="panel-body">
                <p>
                    @lang('borrower.join.form.family-member-description')
                </p>

               <fieldset>
                    <h4>@lang('borrower.join.form.family-member') 1</h4>

                    {{ BootstrapForm::text('familyMember_1_firstName', null, ['label' => 'borrower.join.form.contact.first-name']) }}
                    {{ BootstrapForm::text('familyMember_1_lastName', null, ['label' => 'borrower.join.form.contact.last-name']) }}
                    {{ BootstrapForm::text('familyMember_1_phoneNumber', null, ['label' => 'borrower.join.form.contact.phone-number', 'prepend' => $form->getDialingCode()]) }}
                    {{ BootstrapForm::text('familyMember_1_description', null, ['label' => 'borrower.join.form.contact.relationship']) }}
                </fieldset>

                <hr/>

                <fieldset>
                    <h4>@lang('borrower.join.form.family-member') 2</h4>

                    {{ BootstrapForm::text('familyMember_2_firstName', null, ['label' => 'borrower.join.form.contact.first-name']) }}
                    {{ BootstrapForm::text('familyMember_2_lastName', null, ['label' => 'borrower.join.form.contact.last-name']) }}
                    {{ BootstrapForm::text('familyMember_2_phoneNumber', null, ['label' => 'borrower.join.form.contact.phone-number', 'prepend' => $form->getDialingCode()]) }}
                    {{ BootstrapForm::text('familyMember_2_description', null, ['label' => 'borrower.join.form.contact.relationship']) }}
                </fieldset>

                <hr/>

                <fieldset>
                    <h4>@lang('borrower.join.form.family-member') 3</h4>

                    {{ BootstrapForm::text('familyMember_3_firstName', null, ['label' => 'borrower.join.form.contact.first-name']) }}
                    {{ BootstrapForm::text('familyMember_3_lastName', null, ['label' => 'borrower.join.form.contact.last-name']) }}
                    {{ BootstrapForm::text('familyMember_3_phoneNumber', null, ['label' => 'borrower.join.form.contact.phone-number', 'prepend' => $form->getDialingCode()]) }}
                    {{ BootstrapForm::text('familyMember_3_description', null, ['label' => 'borrower.join.form.contact.relationship']) }}
                </fieldset>

            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    @lang('borrower.join.form.neighbor-title')
                </h3>
            </div>
            <div class="panel-body">
                <p>
                    @lang('borrower.join.form.neighbor-description')
                </p>

                <fieldset>
                    <h4>@lang('borrower.join.form.neighbor') 1</h4>

                    {{ BootstrapForm::text('neighbor_1_firstName', null, ['label' => 'borrower.join.form.contact.first-name']) }}
                    {{ BootstrapForm::text('neighbor_1_lastName', null, ['label' => 'borrower.join.form.contact.last-name']) }}
                    {{ BootstrapForm::text('neighbor_1_phoneNumber', null, ['label' => 'borrower.join.form.contact.phone-number', 'prepend' => $form->getDialingCode()]) }}
                    {{ BootstrapForm::text('neighbor_1_description', null, ['label' => 'borrower.join.form.contact.relationship']) }}
                </fieldset>

                <hr/>

                <fieldset>
                    <h4>@lang('borrower.join.form.neighbor') 2</h4>

                    {{ BootstrapForm::text('neighbor_2_firstName', null, ['label' => 'borrower.join.form.contact.first-name']) }}
                    {{ BootstrapForm::text('neighbor_2_lastName', null, ['label' => 'borrower.join.form.contact.last-name']) }}
                    {{ BootstrapForm::text('neighbor_2_phoneNumber', null, ['label' => 'borrower.join.form.contact.phone-number', 'prepend' => $form->getDialingCode()]) }}
                    {{ BootstrapForm::text('neighbor_2_description', null, ['label' => 'borrower.join.form.contact.relationship']) }}
                </fieldset>

                <hr/>

                <fieldset>
                    <h4>@lang('borrower.join.form.neighbor') 3</h4>

                    {{ BootstrapForm::text('neighbor_3_firstName', null, ['label' => 'borrower.join.form.contact.first-name']) }}
                    {{ BootstrapForm::text('neighbor_3_lastName', null, ['label' => 'borrower.join.form.contact.last-name']) }}
                    {{ BootstrapForm::text('neighbor_3_phoneNumber', null, ['label' => 'borrower.join.form.contact.phone-number', 'prepend' => $form->getDialingCode()]) }}
                    {{ BootstrapForm::text('neighbor_3_description', null, ['label' => 'borrower.join.form.contact.relationship']) }}
                </fieldset>
            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    @lang('borrower.join.form.more-info')
                </h3>
            </div>
            <div class="panel-body">

                {{ BootstrapForm::text('preferredLoanAmount', null, ['append' => $form->getCountry()->getCurrencyCode()]) }}
                {{ BootstrapForm::text('preferredInterestRate', null, ['append' => '%']) }}
                {{ BootstrapForm::text('preferredRepaymentAmount') }}
                {{ BootstrapForm::select('businessCategoryId', $form->getCategories()) }}
                {{ BootstrapForm::select('businessYears', $form->getBusinessYears()) }}
                {{ BootstrapForm::select('loanUsage', $form->getLoanUsage()) }}
                {{--{{ BootstrapForm::datepicker('birthDate') }}--}}
    
            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    @lang('borrower.join.form.terms-and-condition.legend')
                </h3>
            </div>
            <div class="panel-body">

                <div class="checkbox">
                    <label>
                        <input id="termsAndConditionCheckbox" name="termsAndCondition" type="checkbox">
                            {{ \Lang::get('borrower.join.form.terms-and-condition.confirmation') }}
                            <a data-toggle="modal" data-target="#termsAndConditionModal">{{ \Lang::get('borrower.join.form.terms-and-condition.title') }}</a>.
                    </label>
                </div>
    
            </div>
        </div>

        {{ BootstrapForm::submit('submit') }}
        {{ BootstrapForm::submit('save-later', ['class' => 'btn btn-default']) }}

        {{ BootstrapForm::close() }}

    </div>
</div>

@include('partials._modal', [
    'id' => 'termsAndConditionModal',
    'title' => \Lang::get('borrower.join.form.terms-and-condition.title'),
    'body' => \Lang::get('borrower.join.form.terms-and-condition.body')
])

<!-- TO DO: we should display this in the FB link page along with a link to the currently linked FB account, and only if one has already been linked 
<p>
    <button type="submit" class="btn btn-facebook" name="disconnect-facebook" value="disconnect-facebook">
        <span class="fa fa-facebook fa-lg fa-fw"></span>
        @lang('borrower.join.form.disconnect-facebook')
    </button>
</p>
-->

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
