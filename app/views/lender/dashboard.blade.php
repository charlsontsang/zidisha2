@extends('layouts.side-menu-simple')

@section('page-title')
Dashboard
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.lender-links')
@stop

@section('page-content')

@if (empty($totalLentAmount))
<div class="panel panel-info">
    <div class="panel-body">
        Ready to make a loan? Check out our fundraising projects here: <a href="{{ route('lender:edit-profile') }}" class="btn btn-primary pull-right">View projects</a>
    </div>
</div>
@endif

@if (empty (Auth::getUser()->getLender()->getProfile()->getAboutMe()))
<div class="panel panel-info">
    <div class="panel-body">
        Introduce yourself to our entrepreneurs! <a href="{{ route('lender:edit-profile') }}" class="btn btn-primary pull-right">Fill out profile</a>
    </div>
</div>
@endif

@if (empty($numberOfInvitesSent))
<div class="panel panel-info">
    <div class="panel-body">
        Send a free $25 lending credit to a friend. <a href="{{ route('lender:invite') }}" class="btn btn-primary pull-right">Learn more</a>
    </div>
</div>
@endif

@if (count($comments))
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">
                Recent Updates
            </h3>
        </div>
        <div class="panel-body">
            @foreach($comments as $comment)
                @include('partials.comments.display-comment-partial', ['comment' => $comment])
            @endforeach
        </div>
    </div>
@endif

@stop
