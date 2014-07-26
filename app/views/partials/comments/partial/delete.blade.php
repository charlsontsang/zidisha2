<div class="comment-form" style="display: none;" data-comment-action="delete" >
    {{ BootstrapForm::open(array('action' => [ $controller.'@postDelete', 'id' => $receiver->getId()  ], 'translationDomain' => 'comments')) }}
    {{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
    {{ BootstrapForm::submit('delete', ['class' => 'btn-danger', 'data-submit' => '', 'data-loading-text' => \Lang::get('borrower.comments.loading-text.delete')]) }}
    {{ BootstrapForm::close() }}
</div>
