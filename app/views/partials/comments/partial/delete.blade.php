<div class="comment-form" style="display: none;" data-comment-action="delete" >
    {{ BootstrapForm::open(array('action' => [ $controller.'@postDelete', 'id' => $receiver->getId() ], 'translationDomain' => 'borrower.comments')) }}
    {{ BootstrapForm::label(\Lang::get('borrower.comments.delete-comment')) }}
    {{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
    {{ BootstrapForm::submit('actions.delete', ['class' => 'btn btn-danger', 'data-loading-text' => \Lang::get('borrower.comments.loading-text.delete')]) }}
    {{ BootstrapForm::close() }}
</div>
