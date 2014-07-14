<div class="comment-form" data-comment-action="edit" style="display: none;">
{{ BootstrapForm::open(array('route' => 'comment:edit', 'translationDomain' => 'comments', 'files' => true)) }}

{{ BootstrapForm::textarea('message', $comment->getMessage()) }}
{{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
{{ BootstrapForm::hidden('commentType', $commentType) }}

    <div class="comment-upload-inputs">
        {{ BootstrapForm::file('file[]', ['label' => 'comments.upload-file']) }}
    </div>
    <button class="btn btn-primary btn-success comment-upload-add-more">@lang('comments.add-more')</button>
{{ BootstrapForm::submit('edit') }}

{{ BootstrapForm::close() }}
</div>