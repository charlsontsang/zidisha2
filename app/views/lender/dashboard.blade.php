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
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">
                Your Project Updates
            </h3>
        </div>
        <div class="panel-body">
            @if (count($comments))
                @foreach($comments as $comment)
                    @include('partials.comments.display-comment-partial', ['comment' => $comment])
                @endforeach
            @else
                <p>Your comment feed is empty.</p>
                <p><strong><a href="{{ route('lend:index') }}">Make a Loan</a></strong></p>
            @endif
        </div>
    </div>
@stop
