<div class="comment-form" data-comment-action="reply" style="display: none;">
    {{ BootstrapForm::open(array('action' => [ $controller.'@postReply', 'id' => $receiver->getId()  ], 'translationDomain' => 'comments')) }}

    {{ BootstrapForm::textarea('message') }}
    {{ BootstrapForm::hidden('receiver_id', $receiver->getId()) }}
    {{ BootstrapForm::hidden('parent_id', $comment->getId()) }}
    {{ BootstrapForm::submit('reply', ['data-submit' => '', 'data-loading-text' => \Lang::get('comments.reply.loading-text')]) }}

    {{ BootstrapForm::close() }}
</div>
