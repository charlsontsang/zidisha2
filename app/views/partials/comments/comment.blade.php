<li id="comment-{{ $comment->getId() }}" class="comment">
        <div>
            <div class="media">
                @if($comment->getUser())
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
                        @if($comment->getUser())
                            <a href="{{ $comment->getUser()->getProfileUrl() }}">{{ $comment->getUser()->getUsername() }}</a>
                        @endif
                        <small>{{ $comment->getCreatedAt()->format('M d, Y') }}</small>
                    </h4>
                    @if($comment->isTranslated())
                        <p>
                            {{{ $comment->getMessageTranslation() }}}
                        </p>
                        <p class="clearfix">
                            <small class="pull-right">
                                <em>@lang('comments.actions.translated-by')</em>
                                {{ link_to($comment->getUser()->getProfileUrl(), $comment->getUser()->getUsername()) }}
                                &nbsp;&nbsp;&nbsp;
                                <a href="#" class="comment-original-message">@lang('comments.actions.show-original')</a>
                            </small>
                        </p>
                        <p style="display: none">
                            {{{ $comment->getMessage() }}}
                        </p>
                    @else
                        <p>
                            {{{ $comment->getMessage() }}}
                        </p>
                    @endif

                    @include("partials.comments.partial.display-uploads", ['comment' => $comment])

                    @if(!$comment->isOrphanDeleted())
                        <div class="comment-actions">
                                @include("partials.comments.partial.comment-actions", ['comment' => $comment])
                        </div>
                        <div class="comment-forms">
                            @include("partials.comments.partial.comment-action-forms", ['borrower' => $borrower, 'comment' => $comment])
                        </div>
                    @endif
                </div>
            </div>

        </div>
    <ul>
        @foreach($comment->getChildren() as $child)
            @include("partials.comments.comment", ['comment' => $child])
        @endforeach
    </ul>
</li>
