<div class="comments">
    @if($canPostComment)
        @include('partials.comments.partial.post', ['controller' => $controller, 'receiver' => $receiver,  'canPostComment' => $canPostComment, 'canReplyComment' => $canReplyComment])
    @else
        <b>Please Login to comment</b>
    @endif


    <ul class="list-unstyled">
        @foreach($comments as $comment)
            @include("partials.comments.root", [
                'comment' => $comment,
                'controller' => $controller,
                'receiver' => $receiver,
                'canPostComment' => $canPostComment,
                'canReplyComment' => $canReplyComment
            ])
        @endforeach
    </ul>
</div>
{{ BootstrapHtml::paginator($comments)->links() }}

<script type="text/html" id="comment-upload-input-template">
    {{ BootstrapForm::file('file[]', ['label' => 'comments.upload-file']) }}
</script>
