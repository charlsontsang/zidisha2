<div class="comment-form" style="display: none;" data-comment-action="translate" >
    {{ BootstrapForm::open(array('action' => [ $controller.'@postTranslate', 'id' => $receiver->getId()  ], 'translationDomain' => 'comments')) }}

    {{ BootstrapForm::textarea('message') }}
    {{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
    {{ BootstrapForm::submit('translate') }}

    {{ BootstrapForm::close() }}
</div>