@extends('layouts.side-menu')

@section('page-title')
Project Updates
@stop

@section('menu-title')
About
@stop

@section('menu-links')
@include('partials.nav-links.community-links')
@stop

@section('page-content')
<div class="info-page">
    <p>
    	We’re not just about spreading the wealth — we’re all about spreading friendship, too.  That’s why we make it easy for lenders and borrowers to communicate directly with each other throughout the duration of their loans and beyond.  Scroll down to see ambition in action!
        <br/><br/>
    </p>
</div>

@foreach($comments as $comment)
    @include('partials.comments.display-comment-partial', ['comment' => $comment])
@endforeach

{{ BootstrapHtml::paginator($comments)->links() }}
@stop
