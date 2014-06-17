<div class="comment-form" style="display: none;" data-comment-action="delete" >
    {{ BootstrapForm::open(array('route' => 'comment:delete', 'translationDomain' => 'comments')) }}
    {{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
    {{ BootstrapForm::submit('delete', ['class' => 'btn-danger']) }}
    {{ BootstrapForm::close() }}
</div>
