<div class="edit-reply">
{{ BootstrapForm::open(array('route' => 'comment:edit', 'translationDomain' => 'comments')) }}

{{ BootstrapForm::text('message') }}
{{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
{{ BootstrapForm::submit('edit') }}

{{ BootstrapForm::close() }}
</div>