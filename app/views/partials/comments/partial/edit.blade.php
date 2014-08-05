<div class="comment-form" data-comment-action="edit" style="display: none;">
{{ BootstrapForm::open(array('action' => [ $controller.'@postEdit', 'id' => $receiver->getId()  ], 'translationDomain' => 'borrower.comments', 'files' => true)) }}

{{ BootstrapForm::textarea('message', $comment->getMessage()) }}
{{ BootstrapForm::hidden('comment_id', $comment->getId()) }}

<div class="clearfix">
        
    @if($controller == 'LoanFeedbackController')
        {{ BootstrapForm::select('rating', array('positive' => 'Positive', 'neutral' => 'Neutral', 'negative' => 'Negative'), 'positive') }}
    @else
    <a href="" data-display='display' target='#edit-comment-{{ $comment->getId() }}-upload-inputs'>
        <i class="fa fa-camera"></i> @lang('borrower.comments.add-photo')
    </a>
    <div class="comment-upload-inputs" id="edit-comment-{{ $comment->getId() }}-upload-inputs" style="display: none;">
        {{ BootstrapForm::file('file[]', ['label' => 'borrower.comments.upload-file']) }}
        <button class="btn btn-primary btn-success comment-upload-add-more">@lang('borrower.comments.add-more')</button>
    </div>
    @endif
    
    <div class="pull-right">
        {{ BootstrapForm::submit('actions.edit', ['data-submit' => '', 'data-loading-text' => \Lang::get('borrower.comments.loading-text.edit')]) }}
    </div>

</div>

    {{ BootstrapForm::close() }}
</div>
