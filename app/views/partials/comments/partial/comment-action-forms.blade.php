@if($canPostComment)
    @if($canReplyComment)
        @include('partials.comments.partial.reply', ['receiver' => $receiver, 'comment' => $comment] )
    @endif
    @if($comment->getUser() == \Auth::user())
        @include('partials.comments.partial.edit', ['comment' => $comment] )
        @include('partials.comments.partial.delete')
    @endif
    @if(\Auth::user()->isLender())
        @include('partials.comments.partial.translate', ['comment' => $comment] )
    @endif
@endif
