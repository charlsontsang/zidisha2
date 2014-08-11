<div class="comment-form clearfix" data-comment-action="reply" style="display: none;">
    {{ BootstrapForm::open(array('action' => [ $controller.'@postReply', 'id' => $receiver->getId()  ], 'translationDomain' => 'borrower.comments', 'files' => true)) }}

    {{ BootstrapForm::textarea('message') }}
    {{ BootstrapForm::hidden('receiver_id', $receiver->getId()) }}
    {{ BootstrapForm::hidden('parent_id', $comment->getId()) }}

    @if($controller != 'LoanFeedbackController')
        <a href="#" data-display='display' target='.reply-comment-upload-inputs'>
            <i class="fa fa-camera"></i> @lang('borrower.comments.add-photo')
        </a>
        <div class="comment-upload-inputs reply-comment-upload-inputs" style="display: none;">
            {{ BootstrapForm::file('file[]', ['label' => 'borrower.comments.upload-file']) }}
            <button class="btn btn-primary btn-success comment-upload-add-more">@lang('borrower.comments.add-more')</button>
        </div>
    @endif
    
    <div class="pull-right">
        {{ BootstrapForm::submit('actions.reply', ['data-submit' => '', 'data-loading-text' => \Lang::get('borrower.comments.loading-text.reply')]) }}
    </div>
    
    {{ BootstrapForm::close() }}
</div>
