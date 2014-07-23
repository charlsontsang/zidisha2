@if($canPostComment)
    @if($canReplyComment)
        @include('partials.comments.partial.reply', ['receiver' => $receiver, 'comment' => $comment] )
    @endif
        @if($comment->getUser() == \Auth::user())
            @include('partials.comments.partial.edit', ['comment' => $comment] )
                @if(\Auth::user()->getRole() == 'lender')
                    @include('partials.comments.partial.translate', ['comment' => $comment] )
                @endif
            @include('partials.comments.partial.delete')
        @endif
@endif
