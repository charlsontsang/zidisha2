@extends('layouts.master')

@section('page-title')
Publish Comments
@stop

@section('content')
<div class="page-header">
    <h1>
        Project Updates
    </h1>
</div>

<div class="row">
    @foreach($comments as $comment)
        @include('partials.comments.display-comment-partial', ['comment' => $comment])
    <br/>
    @endforeach

    {{ BootstrapHtml::paginator($comments)->links() }}
</div>
@stop
