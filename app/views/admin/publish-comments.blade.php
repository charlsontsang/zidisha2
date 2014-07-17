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
                <div class="row">
                    <div class="col-xs-9">
                        @include('partials.comments.display-comment-partial', ['comment' => $comment])
                    </div>
                    <div class="col-xs-3">
                        {{ BootstrapForm::open(array('route' => 'admin:post:moderate-comments', 'translationDomain' => 'publish-comments')) }}
                        {{ BootstrapForm::hidden('borrowerCommentId', $comment->getId()) }}
                        {{ BootstrapForm::submit('publish') }}
                        {{ BootstrapForm::close() }}
                    </div>
                </div>
                <br/>
            @endforeach
    {{ BootstrapHtml::paginator($comments)->links() }}
</div>
@stop
