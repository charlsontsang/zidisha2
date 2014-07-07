@extends('layouts.master')

@section('page-title')
Pending Borrower Activation
@stop

@section('content')
<div class="row">
    <div class="col-xs-8">

        <dl class="dl-horizontal">
            <dt>Applicant Name</dt> 
            <dd>{{ $borrower->getName() }}</dd>

            <dt>Telephone</dt>
            <dd>{{ $borrower->getProfile()->getPhoneNumber() }}</dd>

            <dt>Email</dt>
            <dd>{{ $borrower->getUser()->getEmail() }}</dd>

            <dt>City</dt>
            <dd>{{ $borrower->getProfile()->getCity() }}</dd>

            <dt>Country</dt>
            <dd>{{ $borrower->getCountry()->getName() }}</dd>

            <dt>Application Status</dt>
            <dd>{{ $borrower->getActivationStatus() }}</dd>
        </dl>

    </div>

    <div class="col-xs-4">
        <img width="200" height="200" src="{{ $borrower->getUser()->getProfilePictureUrl() }}">
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <h2>Step 1: Address Check</h2>
        <p>
            Does the address describe the applicant's home location precisely enough for a stranger to locate the applicant’s home?<br/><br/>
            The address should be a home location, not a business location or a P.O. box.
            If the address does not indicate a precise street/neighborhood AND house number or plot number,
            then it should include a detailed enough description to allow a stranger to easily locate the applicant's home.
        </p>
        <p class="well">
            {{{ $borrower->getProfile()->getAddress() }}}
            <br/>
            {{{ $borrower->getCountry()->getName() }}}
            <br/>
            {{{ $borrower->getProfile()->getAddressInstructions() }}}
        </p>
    </div>
</div>

<div class="row">
    <div class="col-xs-4">
        {{ BootstrapForm::open(['route' => ['admin:borrower-activation:review', $borrower->getId()]]) }}
        {{ BootstrapForm::populate($reviewForm) }}
        
        {{ BootstrapForm::radio('isAddressLocatable', true, null, ['label' => 'Yes']) }}
        {{ BootstrapForm::radio('isAddressLocatable', false, null, ['label' => 'No']) }}
        
        {{ BootstrapForm::textarea('isAddressLocatableNote', null, ['label' => 'Optional note', 'rows' => '3']) }}
        
        {{ BootstrapForm::submit('Submit') }}
        
        {{ BootstrapForm::close() }}
    </div>
</div>

@if($borrower->getReview())
<br/>
<div class="row" id="review">
    <div class="col-xs-12">
        @if($borrower->getReview()->isCompleted())
        <div class="alert alert-info">
            Step 1: Address Check is complete. Please proceed to Step 2: Verification.
        </div>
        @else
        <div class="alert alert-info">
            Please use the form below to contact the applicant and request the missing information.
        </div>
        @endif
    </div>
</div>

@include('admin.borrower-activation.feedback', compact('feedbackForm', 'borrower', 'feedbackMessages'))
@endif

@if($borrower->isActivationReviewed())
    @include('admin.borrower-activation.verification', compact('verificationForm', 'borrower'))
@endif
@stop
