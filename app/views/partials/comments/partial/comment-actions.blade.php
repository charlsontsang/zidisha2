<div class="text-muted">
    @if(\Auth::check())
        @if($comment->getUser() == \Auth::user())
            <a href="#edit">edit</a> &bull;
            <a href="#delete">delete</a> &bull;
        @endif
    @endif
    <a href="#reply">reply</a> &bull;
    <a href="#share">share</a>
</div>