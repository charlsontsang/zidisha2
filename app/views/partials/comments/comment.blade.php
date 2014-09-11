@if($controller == 'LoanFeedbackController')
<li id="feedback-{{ $comment->getId() }}" class="comment media">
@else
<li id="comment-{{ $comment->getId() }}" class="comment media">
@endif

    <div class="pull-left">
        @if($comment->getUser() && !$comment->getUser()->isAdmin())
            <a href="{{ $comment->getUser()->getProfileUrl() }}">
                <img class="media-object" src="{{ $comment->getUser()->getProfilePictureUrl() }}" alt="">
            </a>
        @else
        <a>
            <img class="media-object" src="{{ asset('/assets/images/default.jpg') }}" alt="">
        </a>
        @endif
    </div>

    <div class="media-body">                    
        <h4 class="media-heading">
            @if($comment->getUser() && !$comment->getUser()->isAdmin())
                <a href="{{ $comment->getUser()->getProfileUrl() }}">{{ $comment->getUser()->getUsername() }}</a>
            @else
                 Deleted
            @endif
            <small>{{ $comment->getCreatedAt()->format('M d, Y') }}</small>
        </h4>


        @if($controller == 'LoanFeedbackController' && $comment->isRoot() && !$comment->getRemoved())
            <?php
                $labelClass = 'default';
                if ($comment->getRating() == \Zidisha\Comment\LoanFeedbackComment::POSITIVE) {
                    $labelClass = 'success';
                } elseif ($comment->getRating() == \Zidisha\Comment\LoanFeedbackComment::NEGATIVE) {
                    $labelClass = 'danger';
                }
            ?>
            <span class="label label-{{ $labelClass }}">
                @lang('borrower.loan.feedback.' . $comment->getRating())
            </span>
        @endif
        
        @if($comment->isTranslated())
            <p>
                {{ nl2br(e($comment->getMessageTranslation())) }}
            </p>
            <p class="clearfix">
                <small class="pull-right">
                    <em>Translated by </em>
                    @if(!$comment->getTranslator()->isAdmin())
                        {{ link_to($comment->getTranslator()->getProfileUrl(), $comment->getTranslator()->getUsername()) }}
                    @else
                        {{ $comment->getTranslator()->getUsername() }}
                    @endif
                    &nbsp;&nbsp;&nbsp;
                    <a href="#" class="comment-original-message">Show original</a>
                </small>
            </p>
            <p style="display: none">
                {{ nl2br(e($comment->getMessage())) }}
            </p>
        @else
            <p>
                {{ nl2br(e($comment->getMessage())) }}
            </p>
        @endif

        @if($controller != 'LoanFeedbackController')
            @include("partials.comments.partial.display-uploads", ['comment' => $comment, 'canPostComment' => $canPostComment, 'canReplyComment' => $canReplyComment, 'controller' => $controller, 'receiver' => $receiver ])
        @endif


        @if(!$comment->isOrphanDeleted())
            <div class="comment-actions">
                    @include("partials.comments.partial.comment-actions", ['comment' => $comment, 'controller' => $controller,  'canPostComment' => $canPostComment, 'canReplyComment' => $canReplyComment])
            </div>
            <div class="comment-forms">
                @include("partials.comments.partial.comment-action-forms", ['receiver' => $receiver, 'comment' => $comment, 'controller' => $controller,  'canPostComment' => $canPostComment, 'canReplyComment' => $canReplyComment])
            </div>
        @endif
        
        <ul class="media-list">
            @foreach($comment->getChildren() as $child)
            @include("partials.comments.comment", ['comment' => $child, 'controller' => $controller, 'canPostComment' => $canPostComment, 'canReplyComment' => $canReplyComment])
            @endforeach
        </ul>
    </div>
</li>
