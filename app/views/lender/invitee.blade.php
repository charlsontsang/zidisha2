@extends('layouts.master')

@section('page-title')
You're invited!
@stop

@section('content')
<div class="wrapper" style="text-align: center;">
    @if($lender->getUser()->hasProfilePicture())
    <img class="profile-image" style="width:100px" src="{{ $lender->getUser()->getProfilePictureUrl() }}"/>
    <br/><br/>
    @endif
    <h3>
        YOU HAVE RECEIVED A $25 LENDING CREDIT FROM
        <a href="{{ route('lender:public-profile', $lender->getUser()->getUsername()) }} ">
            {{ $lender->getUser()->getUsername()}}
        </a>
    </h3>
</div>

<div>
    <div style="text-align: center;margin-top: 20px;">
        Welcome to Zidisha! We are pioneering the first online microlending community to connect lenders and borrowers
        directly across international borders - overcoming previously insurmountable barriers of geography, wealth and
        circumstance.
        <br/><br/>
        To redeem your credit, simply create a free lending account. You'll receive a $25 credit to fund a loan project
        of your choice, and you can follow the project's progress as the loan is repaid to the organization. If you
        enjoy helping our entrepreneurs achieve their dreams, we hope you'll return to make another loan in the future.

        &nbsp;&nbsp;&nbsp;&nbsp;
        <a href="{{ route('lender:how-it-works') }}">Learn more</a>
        <br/><br/>
        <a href="{{ route('join') }}" class="btn btn-primary">
            Create Account
        </a>
    </div>
</div>
@stop
