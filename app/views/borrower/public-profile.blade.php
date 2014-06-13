@extends('layouts.master')

@section('page-title')
Join the global P2P microlending movement
@stop

@section('content')
<div class="page-header">
    <h1>Borrower Details</h1>
</div>

@if($borrower->getUser()->hasProfilePicture())
<img src="{{ $borrower->getUser()->getProfilePicture() }}">
@endif

<p><strong>Username: </strong> {{ $borrower->getUser()->getUsername() }} </p> <br>

<p><strong>About me: </strong> {{ $borrower->getProfile()->getAboutMe() }} </p> <br>

<p><strong>About business: </strong> {{ $borrower->getProfile()->getAboutBusiness() }} </p> <br>
@stop
