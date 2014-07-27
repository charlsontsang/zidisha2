@extends('layouts.master')

@section('page-title')
Project Updates
@stop

@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="info-page">
            <h1>
                Project Updates
            </h1>
            <p>
            	We’re not just about spreading the wealth — we’re all about spreading friendship, too! That’s why we make it easy for lenders and borrowers to communicate directly with each other throughout the duration of their loans and beyond. Here, lenders can see their seed money grow into something life changing. And borrowers can show off their workspaces and update everyone on their goals and progress. Scroll down to see ambition in action!
            </p>
        </div>

        @foreach($comments as $comment)
            @include('partials.comments.display-comment-partial', ['comment' => $comment])
        <br/>
        @endforeach

        {{ BootstrapHtml::paginator($comments)->links() }}
    </div>
</div>
@stop
