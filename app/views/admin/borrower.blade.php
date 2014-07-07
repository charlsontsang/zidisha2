@extends('layouts.master')

@section('page-title')
{{ $borrower->getName() }}
@stop

@section('content')
<div class="page-header">
    <h1>Borrower Details</h1>
</div>

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

@if(!$borrower->getUploads()->isEmpty())
<h4>Borrower Pictures</h4>
<div>
    @foreach($borrower->getUploads() as $upload)
    @if($upload->isImage())
    <a href="{{ $upload->getImageUrl('small-profile-picture') }}">
        <img src="{{ $upload->getImageUrl('small-profile-picture') }}" alt=""/>
    </a>
    @else
    <div class="well">
        <a href="{{  $upload->getFileUrl()  }}">{{ $upload->getFilename() }}</a>
    </div>
    @endif
    @endforeach
</div>
@endif
@stop
