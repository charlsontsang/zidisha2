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
{{ BootstrapForm::text('phoneNumber', null, ['prepend' => '+ ' . $form->getCountry()->getDialingCode() . ' (0)']) }}
{{ BootstrapForm::text('alternatePhoneNumber', null, [
    'prepend' => '+ ' . $form->getCountry()->getDialingCode() . ' (0)',
    'description' => \Lang::get('borrowerJoin.form.optional')
]) }}

<br><br>
<p>REFERENCES</p>
{{ BootstrapForm::select('members') }}
{{ BootstrapForm::select('volunteer_mentor_city', $form->getVolunteerMentorCity()) }}
{{ BootstrapForm::select('volunteer_mentor') }}

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
