<li id="comment-{{ $comment->getId() }}" class="comment media">
    <div class="pull-left">
        @if($comment->getUser() && !$comment->getUser()->isAdmin())
            @if($comment->getUser()->getProfileUrl())
                <a href="{{ $comment->getUser()->getProfileUrl() }}">
                    <img class="media-object" src="{{ $comment->getUser()->getProfilePictureUrl() }}" alt="">
                </a>
            @else
                <img class="media-object" src="{{ $comment->getUser()->getProfilePictureUrl() }}" alt="">
            @endif
        @else
            <img class="media-object" src="{{ asset('/assets/images/profile-default/profile-default.jpg') }}" alt="">
        @endif
    </div>

    <div class="media-body">
        <h4 class="media-heading">
            @if($comment->getUser() && !$comment->getUser()->isAdmin())
                @if($comment->getUser()->getProfileUrl())
                    <a href="{{ $comment->getUser()->getProfileUrl() }}">
                        {{ $comment->getUser()->getUsername() }}
                    </a>
                @else
                    {{ $comment->getUser()->getUsername() }}
                @endif
            @else
                Deleted
            @endif
            <small>{{ $comment->getCreatedAt()->format('M d, Y') }}</small>
        </h4>

        <p>
            {{{ $comment->getMessage() }}}
        </p>

        <a href="{{ route('loan:index', ['loanId' => $comment->getBorrower()->getLastLoanId()]) }}#comment-{{$comment->getId()}}">
            Translate this comment
        </a>

        @include("partials.comments.partial.display-uploads", ['comment' => $comment])
    </div>
</li>
