<li id="comment-{{ $comment->getId() }}" class="comment">
    <div>
        <div class="media">
            @if($comment->getUser() && !$comment->getUser()->isAdmin())
            <a class="pull-left" href="{{ $comment->getUser()->getProfileUrl() }}">
                <img class="media-object" width="100px" height="100px" src="{{ $comment->getUser()->getProfilePictureUrl() }}" alt="">
            </a>
            @else
            <a class="pull-left">
                <img class="media-object" width="100px" height="100px" src="{{ asset('/assets/images/default.jpg') }}" alt="">
            </a>
            @endif

            <div class="media-body">
                <h4 class="media-heading">
                    @if($comment->getUser() && !$comment->getUser()->isAdmin())
                    <a href="{{ $comment->getUser()->getProfileUrl() }}">{{ $comment->getUser()->getUsername() }}</a>
                    @else
                        {{ $comment->getUser()->getUsername() }}
                    @endif
                    <small>{{ $comment->getCreatedAt()->format('M d, Y') }}</small>
                </h4>

                <p>
                    {{{ $comment->getMessage() }}}
                </p>

                <a href="#TODO">Translate This Comment</a>

                @include("partials.comments.partial.display-uploads", ['comment' => $comment])
            </div>
        </div>

    </div>
</li>
