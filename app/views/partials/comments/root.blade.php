@include("partials.comments.comment", [
    'comment' => $comment,
    'controller' => $controller,
    'canPostComment' => $canPostComment,
    'canReplyComment' => $canReplyComment
])
<hr/>