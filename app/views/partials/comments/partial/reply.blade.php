<div class="comment-form" data-comment-action="reply" style="display: none;">
    {{ BootstrapForm::open(array('route' => 'comment:reply', 'translationDomain' => 'comments')) }}

    {{ BootstrapForm::textarea('message') }}
    {{ BootstrapForm::hidden('borrower_id', $borrower->getId()) }}
    {{ BootstrapForm::hidden('parent_id', $comment->getId()) }}
    {{ BootstrapForm::submit('reply') }}

    {{ BootstrapForm::close() }}
</div>