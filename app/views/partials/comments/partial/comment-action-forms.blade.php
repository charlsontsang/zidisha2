@if(\Auth::check())
    @include('partials.comments.partial.reply', ['borrower' => $borrower, 'comment' => $comment] )
    @if($comment->getUser() == \Auth::user())
        @include('partials.comments.partial.edit', ['comment' => $comment] )
            @if(\Auth::user()->getRole() == 'lender')
                @include('partials.comments.partial.translate', ['comment' => $comment] )
            @endif
        @include('partials.comments.partial.delete')
    @endif
@endif