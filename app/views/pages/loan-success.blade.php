@extends('layouts.master')

@section('page-title')
    {{ $loan->getBorrower()->getName() }}
@stop

@section('content')
<div class="row">
    <div class="col-sm-6 loan-body">
        <div class="pull-left profile-image" href="{{ route('loan:index', $loan->getId()) }}"
            style="background-image:url({{ $loan->getBorrower()->getUser()->getProfilePictureUrl('large-profile-picture') }})" width="100%">
        </div>
    </div>

    <div class="col-sm-6">
        <div class="lead">
            <p>
                <br/><br/>
                You just supported {{{ $loan->getBorrower()->getFirstName() }}}!
            </p>
            <p>
                Did you know that sharing a loan story makes it 3x more likely to be fully funded?
            </p>
            <p>
                Help {{{ $loan->getBorrower()->getFirstName() }}} by sharing with your friends:
            </p>
            <p>
                <a href="{{$facebook_url}}" class="btn btn-facebook btn-social">
                    <i class="fa fa-fw fa-facebook"></i>Share
                </a>
                <a href="{{$twitter_url}}" class="btn btn-twitter btn-social">
                    <i class="fa fa-fw fa-twitter"></i>Tweet
                </a>
                <a href="{{$mail_url}}" class="btn btn-danger btn-social">
                    <i class="fa fa-fw fa-envelope-o"></i>Email
                </a>
            </p>
        </div>
        <p style="font-size: 18px !important;">
            <br/>
            <a href="{{ route('lend:index') }}">Make another loan</a>
        </p>
    </div>
</div>

@stop