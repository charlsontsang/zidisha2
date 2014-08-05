<div class="comment-form clearfix" data-comment-action="reply" style="display: none;">
    {{ BootstrapForm::open(array('action' => [ $controller.'@postReply', 'id' => $receiver->getId()  ], 'translationDomain' => 'borrower.comments')) }}

    {{ BootstrapForm::textarea('message') }}
    {{ BootstrapForm::hidden('receiver_id', $receiver->getId()) }}
    {{ BootstrapForm::hidden('parent_id', $comment->getId()) }}
    
    <div class="pull-right">
        {{ BootstrapForm::submit('actions.reply', ['data-submit' => '', 'data-loading-text' => \Lang::get('borrower.comments.loading-text.reply')]) }}
    </div>
    
    {{ BootstrapForm::close() }}
</div>
