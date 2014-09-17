<div class="comment">
    {{ BootstrapForm::open(array('action' => [ $controller.'@postComment', 'id' => $receiver->getId()  ], 'translationDomain' => 'borrower.comments', 'files' => true)) }}
    {{ BootstrapForm::hidden('receiver_id', $receiver->getId()) }}

    @if($controller == 'LoanFeedbackController')
        {{ BootstrapForm::select('rating', array('positive' => 'Positive', 'neutral' => 'Neutral', 'negative' => 'Negative'), 'positive') }}
    @endif

    {{ BootstrapForm::textarea('message', null, ['required' => 'required', 'rows' => 5]) }}

    <div class="comment-submit clearfix">
        @if($controller != 'LoanFeedbackController')
            <a href="" data-display='display' target='#post-comment-upload-inputs'>
                <i class="fa fa-camera"></i> @lang('common.comments.add-photo')
            </a>
            <div class="comment-upload-inputs" id="post-comment-upload-inputs" style="display: none;">
                <div class="file-input-block">
                  <div style="display: inline-block;">
                      {{ BootstrapForm::file('file[]', ['label' => 'common.comments.upload-file', 'class' => 'upload-file']) }}
                  </div>
                  <div style="display: inline-block;">
                      <a href="#" data-dismiss='removeFile'>
                          <i class="fa fa-times"></i>
                      </a>
                  </div>
                </div>
                <div style="display: block;">
                    <button class="btn btn-primary btn-success comment-upload-add-more">@lang('common.comments.add-more')</button>
                </div>
            </div>
        @endif

        <div class="pull-right">
            {{ BootstrapForm::submit('submit', ['data-loading-text' => \Lang::get('common.comments.loading-text.post')]) }}            
        </div>
    </div>
    {{ BootstrapForm::close() }}
</div>
