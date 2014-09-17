<div class="comment-form" style="display: none;" data-comment-action="delete" >
    {{ BootstrapForm::open(array('action' => [ $controller.'@postDelete', 'id' => $receiver->getId() ], 'translationDomain' => 'common.comments')) }}
    
    <div class="alert alert-danger">
        @lang('common.comments.delete-comment')
        &nbsp;&nbsp;
        {{ BootstrapForm::submit('actions.delete-confirm', ['class' => 'btn btn-danger', 'data-loading-text' => \Lang::get('common.comments.loading-text.delete')]) }}
    </div>

    {{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
    {{ BootstrapForm::close() }}
</div>
