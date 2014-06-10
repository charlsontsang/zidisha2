@extends('layouts.master')

@section('page-title')
Join the global P2P microlending movement
@stop

@section('content')
<div class="page-header">
    <h1>Lender Details</h1>
</div>

@if($lender)
<p><strong>Username: </strong> {{ $lender->getUser()->getUsername() }} </p> <br>

<p><strong>About me: </strong> {{ $lender->getAboutMe() }} </p> <br>
@else
<p>Wrong Username!</p>
@endif
@stop
