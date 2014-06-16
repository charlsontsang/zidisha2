<li id="comment-{{ $comment->getId() }}">
    <div>
        <div class="media">
            <a class="pull-left" href="{{ $comment->getUser()->getProfileUrl(':public-profile') }}">
                <img class="media-object" width="100px" height="100px" src="{{ $comment->getUser()->getProfilePicture() }}" alt="">
            </a>
            <div class="media-body">
                <h4 class="media-heading">
                    <a href="{{ $comment->getUser()->getProfileUrl(':public-profile') }}">{{ $comment->getUser()->getUsername() }}</a>
                    <small>{{ $comment->getCreatedAt()->format('M d, Y') }}</small>
                </h4>
                <p>
                    {{{ $comment->getMessage() }}}
                </p>
            </div>
            @include("partials.comments.partial.comment-actions", ['comment' => $comment])
        </div>
        @include("partials.comments.partial.comment-action-forms", ['borrower' => $borrower, 'comment' => $comment])
    </div>
    <ul>
        @foreach($comment->getChildren() as $child)
            @include("partials.comments.comment", ['comment' => $child])
        @endforeach
    </ul>
</li>
