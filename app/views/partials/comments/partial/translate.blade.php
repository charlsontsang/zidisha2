<div class="comment-form clearfix" style="display: none;" data-comment-action="translate" >
    {{ BootstrapForm::open(array('action' => [ $controller.'@postTranslate', 'id' => $receiver->getId()  ], 'translationDomain' => 'borrower.comments')) }}

    {{ BootstrapForm::textarea('message', $comment->getMessageTranslation(), ['label' => 'borrower.comments.translation', 'required' => 'required', 'rows' => 5]) }}
    {{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
    
    <div class="pull-right">
        {{ BootstrapForm::submit('actions.translate', ['data-loading-text' => \Lang::get('borrower.comments.loading-text.translate')]) }}
    </div>

    {{ BootstrapForm::close() }}
</div>
