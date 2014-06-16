<div class="comment-delete" >
    {{ BootstrapForm::open(array('route' => 'comment:delete', 'translationDomain' => 'comments')) }}
    {{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
    {{ BootstrapForm::submit('delete') }}
    {{ BootstrapForm::close() }}
</div>