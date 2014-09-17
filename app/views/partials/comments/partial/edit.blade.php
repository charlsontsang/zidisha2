<div class="comment-form" data-comment-action="edit" style="display: none;">
{{ BootstrapForm::open(array('action' => [ $controller.'@postEdit', 'id' => $receiver->getId()  ], 'translationDomain' => 'borrower.comments', 'files' => true)) }}

@if($controller == 'LoanFeedbackController' && $comment->isRoot())
    {{ BootstrapForm::select('rating', array('positive' => 'Positive', 'neutral' => 'Neutral', 'negative' => 'Negative'), $comment->getRating()) }}
@endif
    
{{ BootstrapForm::textarea('message', $comment->getMessage(), ['required' => 'required', 'rows' => 5]) }}
{{ BootstrapForm::hidden('comment_id', $comment->getId()) }}

<div class="clearfix">
        
    @if($controller != 'LoanFeedbackController')
    <a href="" data-display='display' target='#edit-comment-{{ $comment->getId() }}-upload-inputs'>
        <i class="fa fa-camera"></i> @lang('common.comments.add-photo')
    </a>
    <div class="comment-upload-inputs" id="edit-comment-{{ $comment->getId() }}-upload-inputs" style="display: none;">
        {{ BootstrapForm::file('file[]', ['label' => 'common.comments.upload-file']) }}
        <button class="btn btn-primary btn-success comment-upload-add-more">@lang('common.comments.add-more')</button>
    </div>
    @endif
    
    <div class="pull-right">
        {{ BootstrapForm::submit('actions.edit', ['data-loading-text' => \Lang::get('common.comments.loading-text.edit')]) }}
    </div>

</div>

    {{ BootstrapForm::close() }}
</div>
