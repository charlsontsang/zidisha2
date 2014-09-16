<div>
    @if(\Auth::check())
        @if(\Auth::user()->getRole() == 'lender')
            <a href="#" target="translate" class="comment-action">@lang('borrower.comments.actions.translate')</a> &bull;
        @endif
        @if($comment->getUser() == \Auth::user())
            <a href="#" target="edit" class="comment-action">@lang('borrower.comments.actions.edit')</a> &bull;
            <a href="#" target="delete" class="comment-action">@lang('borrower.comments.actions.delete')</a> &bull;
        @endif
        @if($canReplyComment)
            <a href="#" target="reply" class="comment-action">@lang('borrower.comments.actions.reply')</a> &bull;
        @endif
    @endif
    <a href="#" class="comment-share">@lang('borrower.comments.actions.share')</a>
    <span style="display: none">
        <a class="share-popup" href="{{$comment->getFacebookUrl()}}" ><i class="fa fa-facebook-square"></i></a>
        <a class="share-popup" href="{{$comment->getTwitterUrl()}}" ><i class="fa fa-twitter-square"></i></a>
    </span>
</div>
