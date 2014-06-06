@extends('layouts.master')

@section('page-title')
Lender
@stop

@section('content')
<div class="page-header">
    <h1>Lender</h1>
</div>

@if($lender)
<p><strong>Username: </strong> {{ $lender->getFirstName() }} </p> <br>

<p><strong>About me: </strong> {{ $lender->getAboutMe() }} </p> <br>
@else
<p>Wrong Username!</p>
@endif
@stop
