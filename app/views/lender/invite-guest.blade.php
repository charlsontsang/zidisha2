@extends('layouts.master')

@section('page-title')
Invite Your Friends To Zidisha
@stop

@section('content')
<div style="text-align: center;">
    <h2>
        Send a friend $25 in free lending credit!
    </h2>
</div>

<div>
    <div class="entry" style="text-align: center;margin-top: 20px;">
        <div>
            You'll receive a matching $25 credit when they lend.
            &nbsp;&nbsp;&nbsp;&nbsp;
            <a href="{{ route('lender:how-it-works') }}">How it works</a>
        </div>
        <br/>
        <br/>
        <a href="{{ route('login') }}" data-toggle="modal" data-target="#login-modal" class="btn btn-primary">
            Log in to invite friends
        </a>
    </div>
</div>
@stop
