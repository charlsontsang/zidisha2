@if($displayFeedbackComments)
    <div id="feedback" class="loan-section comments">

        <div class="loan-section-title">
            <span class="text-light">Feedback</span>
        </div>

        <div class="loan-section-content">
        </div>

        @include('partials.comments.comments', [
            'comments' => $loanFeedbackComments,
            'receiver' => $loan,
            'controller' => 'LoanFeedbackController',
            'canPostComment' => $canPostFeedback,
            'canReplyComment' => $canReplyFeedback
        ])
    </div>
    <hr/>
@endif
