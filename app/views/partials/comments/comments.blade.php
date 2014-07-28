<div class="loan-section comments">

    <div class="loan-section-title">
        <span class="text-light">Discussion</span>
    </div>

    <div class="loan-section-content">
        @if($canPostComment)
            <span class="text-light">
                Ask {{ $loan->getBorrower()->getFirstName() }} a question about this loan, inquire about the business, or send a simple note of thanks or inspiration.
            </span>
            @include('partials.comments.partial.post', [
                'controller' => $controller,
                'receiver' => $receiver,
                'canPostComment' => $canPostComment,
                'canReplyComment' => $canReplyComment
            ])
        @else
            <span class="text-light">Please <a href="#">log in</a> to comment.</span>
        @endif

    </div>

    <ul class="list-unstyled">
        @foreach($comments as $comment)
            @include("partials.comments.root", [
                'comment' => $comment,
                'controller' => $controller,
                'receiver' => $receiver,
                'canPostComment' => $canPostComment,
                'canReplyComment' => $canReplyComment
            ])
        @endforeach
    </ul>
    {{ BootstrapHtml::paginator($comments)->links() }}

    <script type="text/html" id="comment-upload-input-template">
        {{ BootstrapForm::file('file[]', ['label' => 'comments.upload-file', 'class' => 'upload-file']) }}
    </script>
</div>
