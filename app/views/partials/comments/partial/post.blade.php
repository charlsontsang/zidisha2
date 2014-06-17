<div class="comment">
    {{ BootstrapForm::open(array('route' => 'comment:post', 'translationDomain' => 'comments', 'files' => true)) }}
    {{ BootstrapForm::hidden('borrower_id', $borrower->getId()) }}
    {{ BootstrapForm::textarea('message') }}

    <div class="comment-upload-inputs">
        {{ BootstrapForm::file('file[]', ['label' => 'comments.upload-file']) }}
    </div>
    <button class="btn btn-primary btn-success comment-upload-add-more">@lang('comments.add-more')</button>

    {{ BootstrapForm::submit('submit') }}
    {{ BootstrapForm::close() }}
</div>
