<div class="comment-form" data-comment-action="edit" style="display: none;">
{{ BootstrapForm::open(array('action' => [ $controller.'@postEdit', 'id' => $receiver->getId()  ], 'translationDomain' => 'comments', 'files' => true)) }}

{{ BootstrapForm::textarea('message', $comment->getMessage()) }}
{{ BootstrapForm::hidden('comment_id', $comment->getId()) }}

    @if($controller == 'LoanFeedbackController')
        {{ BootstrapForm::select('rating', array('positive' => 'Positive', 'neutral' => 'Neutral', 'negative' => 'Negative'), 'positive') }}
    @else
    <a href="" data-display='display' target='#edit-comment-{{ $comment->getId() }}-upload-inputs'>Add images or files</a>
    <div class="comment-upload-inputs" id="edit-comment-{{ $comment->getId() }}-upload-inputs" style="display: none;">
        {{ BootstrapForm::file('file[]', ['label' => 'comments.upload-file']) }}
        <button class="btn btn-primary btn-success comment-upload-add-more">@lang('comments.add-more')</button>
    </div>
    @endif
    <div>
        {{ BootstrapForm::submit('edit', ['data-submit' => '', 'data-loading-text' => \Lang::get('borrower.comments.loading-text.edit')]) }}
    </div>

{{ BootstrapForm::close() }}
</div>
