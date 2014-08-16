@extends('layouts.master')

@section('page-title')
    {{ $loan->getBorrower()->getName() }}
@stop

@section('content')
<div class="row">
    <div class="col-sm-8 loan-body">
        
        <div class="pull-left profile-image" href="{{ route('loan:index', $loan->getId()) }}"
            style="background-image:url(/assets/images/test-photos/esther.JPG)" width="100%" height="450px">
        </div>
        <!--
        <img src="{{ $loan->getBorrower()->getUser()->getProfilePictureUrl('large-profile-picture') }}" width="100%">
        -->
    </div>

    <div class="col-sm-4 loan-side">
        <div>
            <p class="lead">
                <br/><br/>
                You just supported {{{ $loan->getBorrower()->getFirstName() }}}!
            </p>
            <p class="lead">
                Did you know that sharing a loan story makes it 3x more likely to be fully funded?
            </p>
            <p class="lead">
                Help {{{ $loan->getBorrower()->getFirstName() }}} by sharing with your friends:
            </p>
            <div class="row">
                <div class="col-xs-6">
                    <a href="{{$facebook_url}}" class="btn btn-primary">
                        <i class="fa fa-fw fa-facebook"></i>Share
                    </a>
                </div>
                <div class="col-xs-6">
                    <a href="{{$twitter_url}}" class="btn btn-primary">
                        <i class="fa fa-fw fa-twitter"></i>Tweet
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

@stop