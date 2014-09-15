<div>
    @if(\Auth::check())
        @if(\Auth::user()->getRole() == 'lender')

            <a href="#" target="translate" class="comment-action">@lang('common.comments.
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stopactions.translate')</a> &bull;
        @endif
        @if($comment->getUser() == \Auth::user())
            <a href="#" target="edit" class="comment-action">@lang('common.comments.
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stopactions.edit')</a> &bull;
            <a href="#" target="delete" class="comment-action">@lang('common.comments.
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stopactions.delete')</a> &bull;
        @endif
        @if($canReplyComment)
            <a href="#" target="reply" class="comment-action">@lang('common.comments.
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stopactions.reply')</a> &bull;
        @endif
    @endif
    <a href="#" class="comment-share">@lang('common.comments.
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stopactions.share')</a>
    <span style="display: none">
        <a class="share-popup" href="{{$comment->getFacebookUrl()}}" ><i class="fa fa-facebook-square"></i></a>
        <a class="share-popup" href="{{$comment->getTwitterUrl()}}" ><i class="fa fa-twitter-square"></i></a>
    </span>
</div>
