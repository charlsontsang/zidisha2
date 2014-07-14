<div class="comment-form" data-comment-action="reply" style="display: none;">
    {{ BootstrapForm::open(array('route' => 'comment:reply', 'translationDomain' => 'comments')) }}

    {{ BootstrapForm::textarea('message') }}
    {{ BootstrapForm::hidden('receiver_id', $receiver->getId()) }}
    {{ BootstrapForm::hidden('commentType', $commentType) }}
    {{ BootstrapForm::hidden('parent_id', $comment->getId()) }}
    {{ BootstrapForm::submit('reply') }}

    {{ BootstrapForm::close() }}
</div>