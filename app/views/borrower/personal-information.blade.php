@extends('layouts.master')

@section('page-title')
Join the global P2P microlending movement
@stop

@section('content')
<div class="page-header">
    <h1>Personal Information</h1>
</div>

@if($facebookJoinUrl)
<div class="has-error">
    <div>
        <a href="{{$facebookJoinUrl }}" class="btn btn-primary">Connect With Facebook </a>
    </div>

    @if(!$borrower->getUser()->getFacebookId())
        <span class="help-block">Please Connect your facebook account.</span>
    @endif
</div>
@endif

<div class="borrower-personal-information-form">
    {{ BootstrapForm::open(array('route' => 'borrower:post-personal-information', 'translationDomain' => 'borrower.personal-information', 'files' => true)) }}
    {{ BootstrapForm::populate($form) }}


    <p>CONTACT INFORMATION</p>
    @if ( $form->isEditable('address') )
         {{ BootstrapForm::text('address', $personalInformation['address']) }}
    @else
        <label>Address </label>
        <p>{{ $personalInformation['address'] }}</p>
    @endif

    <br>

    @if ( $form->isEditable('addressInstruction') )
            {{ BootstrapForm::label(\Lang::get('borrowerJoin.form.addressInstructions')) }}
            <br><br>
            {{ BootstrapForm::textArea('addressInstruction', $personalInformation['addressInstruction']) }}
    @else
        <label> Address Instruction  </label>
        <p>{{ $personalInformation['addressInstruction'] }}</p>
    @endif

    @if ( $form->isEditable('city') )
            {{ BootstrapForm::text('city', $personalInformation['city']) }}
    @else
            <label> City </label>
            <p>{{ $personalInformation['city'] }}</p>
    @endif

    @if ( $form->isEditable('nationalIdNumber') )
            {{ BootstrapForm::text('nationalIdNumber', $personalInformation['nationalIdNumber']) }}
    @else
            <label> National Id Number </label>
            <p>{{ $personalInformation['nationalIdNumber'] }}</p>
    @endif

    @if ( $form->isEditable('phoneNumber') )
            {{ BootstrapForm::text('phoneNumber', $personalInformation['phoneNumber'], ['prepend' => $form->getDialingCode()]) }}
    @else
            <label>Phone Number </label>
            <p>{{ $personalInformation['phoneNumber'] }}</p>
    @endif

    @if ( $form->isEditable('alternatePhoneNumber') )
            {{ BootstrapForm::text('alternatePhoneNumber', $personalInformation['alternatePhoneNumber'], ['prepend' => $form->getDialingCode(), 'description' => \Lang::get('borrowerJoin.form.optional') ]) }}
    @else
            <label>Alternate Phone Number </label>
            <p>{{ $personalInformation['alternatePhoneNumber'] }}</p>
    @endif

    <br><br>
    <label>@lang('borrowerJoin.form.communityLeaderDescription')</label>

    <fieldset>
        <legend>@lang('borrowerJoin.form.communityLeader')</legend>

        @if ( $form->isEditable('communityLeader_firstName') )
                {{ BootstrapForm::text('communityLeader_firstName', $personalInformation['communityLeader_firstName'], ['label' => 'borrowerJoin.form.contact.firstName']) }}
        @else
                <label>Community Leader First Name </label>
                <p>{{ $personalInformation['communityLeader_firstName'] }}</p>
        @endif

        @if ( $form->isEditable('communityLeader_lastName') )
                {{ BootstrapForm::text('communityLeader_lastName', $personalInformation['communityLeader_lastName'], ['label' => 'borrowerJoin.form.contact.firstName']) }}
        @else
                <label>Community Leader Last Name </label>
                <p>{{ $personalInformation['communityLeader_lastName'] }}</p>
        @endif


        @if ( $form->isEditable('communityLeader_phoneNumber') )
            {{ BootstrapForm::text('communityLeader_phoneNumber', $personalInformation['communityLeader_phoneNumber'], ['label' => 'borrowerJoin.form.contact.phoneNumber', 'prepend' => $form->getDialingCode()]) }}
        @else
            <label>Community Leader Phone Number </label>
            <p>{{ $personalInformation['communityLeader_phoneNumber'] }}</p>
        @endif

        @if ( $form->isEditable('communityLeader_description') )
                {{ BootstrapForm::text('communityLeader_description', $personalInformation['communityLeader_description'], ['label' => 'borrowerJoin.form.contact.phoneNumber', 'prepend' => $form->getDialingCode()]) }}
        @else
                <label> Community Leader Description  </label>
                <p>{{ $personalInformation['communityLeader_description'] }}</p>
        @endif

    </fieldset>

    <label>@lang('borrowerJoin.form.familyMemberDescription')</label>

    <fieldset>
        <legend>@lang('borrowerJoin.form.familyMember') 1</legend>

        @if ( $form->isEditable('familyMember_1_firstName') )
            {{ BootstrapForm::text('familyMember_1_firstName', $personalInformation['familyMember_1_firstName'], ['label' => 'borrowerJoin.form.contact.firstName']) }}
        @else
            <label> Family Member 1 First Name </label>
            <p>{{ $personalInformation['familyMember_1_firstName'] }}</p>
        @endif

        @if ( $form->isEditable('familyMember_1_lastName') )
            {{ BootstrapForm::text('familyMember_1_lastName', $personalInformation['familyMember_1_lastName'], ['label' => 'borrowerJoin.form.contact.firstName']) }}
        @else
            <label> Family Member 1 Last Name </label>
            <p>{{ $personalInformation['familyMember_1_lastName'] }}</p>
        @endif

        @if ( $form->isEditable('familyMember_1_phoneNumber') )
                {{ BootstrapForm::text('familyMember_1_phoneNumber', null, ['label' => 'borrowerJoin.form.contact.phoneNumber', 'prepend' => $form->getDialingCode()]) }}
        @else
                <label> Family Member 1 Phone Number </label>
                <p>{{ $personalInformation['familyMember_1_phoneNumber'] }}</p>
        @endif

        @if ( $form->isEditable('familyMember_1_description') )
                {{ BootstrapForm::text('familyMember_1_description', $personalInformation['familyMember_1_description'], ['label' => 'borrowerJoin.form.contact.relationship']) }}
        @else
                <label> Family Member 1 Phone Number </label>
                <p>{{ $personalInformation['familyMember_1_description'] }}</p>
        @endif

    </fieldset>

    <fieldset>
        <legend>@lang('borrowerJoin.form.familyMember') 2</legend>

        @if ( $form->isEditable('familyMember_2_firstName') )
                {{ BootstrapForm::text('familyMember_2_firstName', $personalInformation['familyMember_2_firstName'], ['label' => 'borrowerJoin.form.contact.firstName']) }}
        @else
                <label> Family Member 2 First Name </label>
                <p>{{ $personalInformation['familyMember_2_firstName'] }}</p>
        @endif

        @if ( $form->isEditable('familyMember_2_lastName') )
                {{ BootstrapForm::text('familyMember_2_lastName', $personalInformation['familyMember_2_lastName'], ['label' => 'borrowerJoin.form.contact.lastName']) }}
        @else
                <label> Family Member 2 Last Name </label>
                <p>{{ $personalInformation['familyMember_2_lastName'] }}</p>
        @endif

        @if ( $form->isEditable('familyMember_2_phoneNumber') )
                {{ BootstrapForm::text('familyMember_2_phoneNumber', null, ['label' => 'borrowerJoin.form.contact.phoneNumber', 'prepend' => $form->getDialingCode()]) }}
        @else
                <label> Family Member 2 Phone Number </label>
                <p>{{ $personalInformation['familyMember_2_phoneNumber'] }}</p>
        @endif

        @if ( $form->isEditable('familyMember_2_description') )
                {{ BootstrapForm::text('familyMember_2_description', $personalInformation['familyMember_2_description'], ['label' => 'borrowerJoin.form.contact.relationship']) }}
        @else
                <label> Family Member 2 Phone Number </label>
                <p>{{ $personalInformation['familyMember_2_description'] }}</p>
        @endif

    </fieldset>

    <fieldset>
        <legend>@lang('borrowerJoin.form.familyMember') 3</legend>

        @if ( $form->isEditable('familyMember_3_firstName') )
                {{ BootstrapForm::text('familyMember_3_firstName', $personalInformation['familyMember_3_firstName'], ['label' => 'borrowerJoin.form.contact.firstName']) }}
        @else
                <label> Family Member 3 First Name </label>
                <p>{{ $personalInformation['familyMember_3_firstName'] }}</p>
        @endif

        @if ( $form->isEditable('familyMember_3_lastName') )
                {{ BootstrapForm::text('familyMember_3_lastName', $personalInformation['familyMember_3_lastName'], ['label' => 'borrowerJoin.form.contact.lastName']) }}
        @else
                <label> Family Member 3 Last Name </label>
                <p>{{ $personalInformation['familyMember_3_lastName'] }}</p>
        @endif

        @if ( $form->isEditable('familyMember_3_phoneNumber') )
                {{ BootstrapForm::text('familyMember_3_phoneNumber', null, ['label' => 'borrowerJoin.form.contact.phoneNumber', 'prepend' => $form->getDialingCode()]) }}
        @else
                <label> Family Member 3 Phone Number </label>
                <p>{{ $personalInformation['familyMember_3_phoneNumber'] }}</p>
        @endif

        @if ( $form->isEditable('familyMember_3_description') )
                {{ BootstrapForm::text('familyMember_3_description', $personalInformation['familyMember_3_description'], ['label' => 'borrowerJoin.form.contact.relationship']) }}
        @else
                <label> Family Member 3 Phone Number </label>
                <p>{{ $personalInformation['familyMember_3_description'] }}</p>
        @endif
    </fieldset>

    <label>@lang('borrowerJoin.form.neighborDescription')</label>

    <fieldset>
        <legend>@lang('borrowerJoin.form.neighbor') 1</legend>

        @if ( $form->isEditable('neighbor_1_firstName') )
                {{ BootstrapForm::text('neighbor_1_firstName', $personalInformation['neighbor_1_firstName'], ['label' => 'borrowerJoin.form.contact.firstName']) }}
        @else
                <label> Neighbor 1 First Name </label>
                <p>{{ $personalInformation['neighbor_1_firstName'] }}</p>
        @endif

        @if ( $form->isEditable('neighbor_1_lastName') )
                {{ BootstrapForm::text('neighbor_1_lastName', $personalInformation['neighbor_1_firstName'], ['label' => 'borrowerJoin.form.contact.lastName']) }}
        @else
                <label> Neighbor 1 Last Name </label>
                <p>{{ $personalInformation['neighbor_1_lastName'] }}</p>
        @endif


        @if ( $form->isEditable('neighbor_1_phoneNumber') )
                {{ BootstrapForm::text('neighbor_1_phoneNumber', $personalInformation['neighbor_1_phoneNumber'], ['label' => 'borrowerJoin.form.contact.phoneNumber', 'prepend' => $form->getDialingCode()]) }}
        @else
                <label> Neighbor 1 Phone Number </label>
                <p>{{ $personalInformation['neighbor_1_phoneNumber'] }}</p>
        @endif

        @if ( $form->isEditable('neighbor_1_description') )
                {{ BootstrapForm::text('neighbor_1_description', $personalInformation['neighbor_1_description'], ['label' => 'borrowerJoin.form.contact.relationship']) }}
        @else
                <label> Neighbor 1 Description </label>
                <p>{{ $personalInformation['neighbor_1_description'] }}</p>
        @endif

    </fieldset>

    <fieldset>
        <legend>@lang('borrowerJoin.form.neighbor') 2</legend>

        @if ( $form->isEditable('neighbor_2_firstName') )
                {{ BootstrapForm::text('neighbor_2_firstName', $personalInformation['neighbor_2_firstName'], ['label' => 'borrowerJoin.form.contact.firstName']) }}
        @else
                <label> Neighbor 2 First Name </label>
                <p>{{ $personalInformation['neighbor_2_firstName'] }}</p>
        @endif

        @if ( $form->isEditable('neighbor_2_lastName') )
                {{ BootstrapForm::text('neighbor_2_lastName', $personalInformation['neighbor_2_firstName'], ['label' => 'borrowerJoin.form.contact.lastName']) }}
        @else
        <label> Neighbor 2 Last Name </label>
                <p>{{ $personalInformation['neighbor_2_lastName'] }}</p>
        @endif


        @if ( $form->isEditable('neighbor_2_phoneNumber') )
                {{ BootstrapForm::text('neighbor_2_phoneNumber', $personalInformation['neighbor_2_phoneNumber'], ['label' => 'borrowerJoin.form.contact.phoneNumber', 'prepend' => $form->getDialingCode()]) }}
        @else
                <label> Neighbor 2 Phone Number </label>
                <p>{{ $personalInformation['neighbor_2_phoneNumber'] }}</p>
        @endif

        @if ( $form->isEditable('neighbor_2_description') )
                {{ BootstrapForm::text('neighbor_2_description', $personalInformation['neighbor_2_description'], ['label' => 'borrowerJoin.form.contact.relationship']) }}
        @else
                <label> Neighbor 2 Description </label>
                <p>{{ $personalInformation['neighbor_2_description'] }}</p>
        @endif
    </fieldset>

    <fieldset>
        <legend>@lang('borrowerJoin.form.neighbor') 3</legend>

        @if ( $form->isEditable('neighbor_3_firstName') )
                {{ BootstrapForm::text('neighbor_3_firstName', $personalInformation['neighbor_3_firstName'], ['label' => 'borrowerJoin.form.contact.firstName']) }}
        @else
                <label> Neighbor 3 First Name </label>
                <p>{{ $personalInformation['neighbor_3_firstName'] }}</p>
        @endif

        @if ( $form->isEditable('neighbor_3_lastName') )
                {{ BootstrapForm::text('neighbor_3_lastName', $personalInformation['neighbor_3_firstName'], ['label' => 'borrowerJoin.form.contact.lastName']) }}
        @else
                <label> Neighbor 3 Last Name </label>
                <p>{{ $personalInformation['neighbor_3_lastName'] }}</p>
        @endif


        @if ( $form->isEditable('neighbor_3_phoneNumber') )
                {{ BootstrapForm::text('neighbor_3_phoneNumber', $personalInformation['neighbor_3_phoneNumber'], ['label' => 'borrowerJoin.form.contact.phoneNumber', 'prepend' => $form->getDialingCode()]) }}
        @else
                <label> Neighbor 3 Phone Number </label>
                <p>{{ $personalInformation['neighbor_3_phoneNumber'] }}</p>
        @endif

        @if ( $form->isEditable('neighbor_3_description') )
                {{ BootstrapForm::text('neighbor_3_description', $personalInformation['neighbor_3_description'], ['label' => 'borrowerJoin.form.contact.relationship']) }}
        @else
                <label> Neighbor 3 Description </label>
                <p>{{ $personalInformation['neighbor_3_description'] }}</p>
        @endif
    </fieldset>

    {{ BootstrapForm::submit('update') }}
    {{ BootstrapForm::close() }}
</div>
@stop
