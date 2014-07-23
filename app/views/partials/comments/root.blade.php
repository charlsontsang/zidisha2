<li>
    <ul>
        @include("partials.comments.comment", ['comment' => $comment, 'controller' => $controller,  'canPostComment' => $canPostComment, 'canReplyComment' => $canReplyComment])
    </ul>
</li>
