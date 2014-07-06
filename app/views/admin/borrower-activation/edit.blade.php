@extends('layouts.master')

@section('page-title')
Pending Borrower Activation
@stop

@section('content')
<div class="row">
    <div class="col-xs-8">

        <p><strong>Applicant Name: </strong> {{ $borrower->getName() }} </p> <br>

        <p><strong>Telephone: </strong> {{ $borrower->getProfile()->getPhoneNumber() }} </p> <br>

        <p><strong>Email: </strong> {{ $borrower->getUser()->getEmail() }} </p> <br>

        <p><strong>City: </strong> {{ $borrower->getProfile()->getCity() }} </p> <br>

        <p><strong>Country: </strong> {{ $borrower->getCountry()->getName() }} </p> <br>

        <p><strong>Application Status: </strong> {{ $borrower->getActivationStatus() }} </p> <br>

    </div>

    <div class="col-xs-4">
        <img width="200" height="200" src="{{ $borrower->getUser()->getProfilePictureUrl() }}">
    </div>
@stop
