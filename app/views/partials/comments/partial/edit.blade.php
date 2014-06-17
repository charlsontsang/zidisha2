<div class="comment-form" data-comment-action="edit" style="display: none;">
{{ BootstrapForm::open(array('route' => 'comment:edit', 'translationDomain' => 'comments')) }}

{{ BootstrapForm::textarea('message') }}
{{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
{{ BootstrapForm::submit('edit') }}

{{ BootstrapForm::close() }}
</div>