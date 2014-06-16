@include('partials.comments.partial.reply', ['borrower' => $borrower, 'comment' => $comment] )

@if(\Auth::check())
    @if($comment->getUser() == \Auth::user())
        @include('partials.comments.partial.edit', ['comment' => $comment] )
        @include('partials.comments.partial.delete')
    @endif
@endif