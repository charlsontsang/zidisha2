<div class="panel-group">
    @foreach($feedbackMessages as $i => $feedbackMessage)
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <small>{{ $feedbackMessage->getSentAt()->format('d M y') }}</small>
                <a data-toggle="collapse" href="#feedback-message-{{ $feedbackMessage->getId() }}">
                    {{ $feedbackMessage->getSubject() }}
                </a>
            </h4>
        </div>
        <div id="feedback-message-{{ $feedbackMessage->getId() }}" class="panel-collapse collapse {{ $i ? '' : 'in' }}">
            <div class="panel-body">
                <p>
                    {{ nl2br($feedbackMessage->getMessage()) }}
                </p>
            </div>
        </div>
    </div>
    @endforeach
</div>
