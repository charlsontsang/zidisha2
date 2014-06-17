<div class="comment-form" data-comment-action="edit" style="display: none;">
{{ BootstrapForm::open(array('route' => 'comment:edit', 'translationDomain' => 'comments', 'files' => true)) }}

{{ BootstrapForm::textarea('message', $comment->getMessage()) }}
{{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
{{ BootstrapForm::file('file') }}
{{ BootstrapForm::submit('edit') }}

{{ BootstrapForm::close() }}
</div>