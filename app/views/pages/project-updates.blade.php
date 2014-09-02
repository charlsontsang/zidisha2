@extends('layouts.master')

@section('page-title')
Project Updates
@stop

@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="page-header">
            <h1>Project Updates</h1>
        </div>

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
    </div>
</div>
@stop
