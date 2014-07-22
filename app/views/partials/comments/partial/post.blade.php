<div class="comment">
    {{ BootstrapForm::open(array('action' => [ $controller.'@postComment', 'id' => $receiver->getId()  ], 'translationDomain' => 'comments', 'files' => true)) }}
    {{ BootstrapForm::hidden('receiver_id', $receiver->getId()) }}
    {{ BootstrapForm::textarea('message') }}

    @if($controller == 'LoanFeedbackController')
        {{ BootstrapForm::select('rating', array('positive' => 'Positive', 'neutral' => 'Neutral', 'negative' => 'Negative'), 'positive') }}
    @else
        <div class="comment-upload-inputs">
            {{ BootstrapForm::file('file[]', ['label' => 'comments.upload-file']) }}
        </div>
        <button class="btn btn-primary btn-success comment-upload-add-more">@lang('comments.add-more')</button>
    @endif
    {{ BootstrapForm::submit('submit') }}
    {{ BootstrapForm::close() }}
</div>
