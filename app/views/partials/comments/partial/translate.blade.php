<div class="comment-form" style="display: none;" data-comment-action="translate" >
    {{ BootstrapForm::open(array('route' => 'comment:translate', 'translationDomain' => 'comments')) }}

    {{ BootstrapForm::textarea('message') }}
    {{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
    {{ BootstrapForm::hidden('commentType', $commentType) }}
    {{ BootstrapForm::submit('translate') }}

    {{ BootstrapForm::close() }}
</div>