<div class="comment-form" data-comment-action="edit" style="display: none;">
{{ BootstrapForm::open(array('action' => [ $controller.'@postEdit', 'id' => $receiver->getId()  ], 'translationDomain' => 'comments', 'files' => true)) }}

{{ BootstrapForm::textarea('message', $comment->getMessage()) }}
{{ BootstrapForm::hidden('comment_id', $comment->getId()) }}

    @if($controller == 'LoanFeedbackController')
        {{ BootstrapForm::select('rating', array('positive' => 'Positive', 'neutral' => 'Neutral', 'negative' => 'Negative'), 'positive') }}
    @else
    <div class="comment-upload-inputs">
        {{ BootstrapForm::file('file[]', ['label' => 'comments.upload-file']) }}
    </div>
    @endif
    <button class="btn btn-primary btn-success comment-upload-add-more">@lang('comments.add-more')</button>
{{ BootstrapForm::submit('edit') }}

{{ BootstrapForm::close() }}
</div>