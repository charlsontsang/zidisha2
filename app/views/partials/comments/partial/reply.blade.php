<div class="comment-reply">
    {{ BootstrapForm::open(array('route' => 'comment:reply', 'translationDomain' => 'comments')) }}

    {{ BootstrapForm::text('message') }}
    {{ BootstrapForm::hidden('borrower_id', $borrower->getId()) }}
    {{ BootstrapForm::hidden('parent_id', $comment->getId()) }}
    {{ BootstrapForm::submit('reply') }}

    {{ BootstrapForm::close() }}
</div>