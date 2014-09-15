<div class="row">
    @if($borrower->isActivationIncomplete())
    <div class="col-md-6">
        {{ BootstrapForm::open(array('route' => ['admin:borrower-activation:feedback', $borrower->getId()], 'translationDomain' => 'borrower.mails.application-feedback
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stop')) }}
        {{ BootstrapForm::populate($feedbackForm) }}

        {{ BootstrapForm::email('borrowerEmail') }}
        {{ BootstrapForm::text('cc', null, ['placeholder' => 'Enter emails separated by commas']) }}
        {{ BootstrapForm::email('replyTo') }}
        {{ BootstrapForm::text('subject') }}       
        {{ BootstrapForm::textarea('message', null, ['description' => \Lang::get('borrower.mails.application-feedback
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stop.message-description')]) }}
        {{ BootstrapForm::text('senderName') }}

        {{ BootstrapForm::submit('send') }}

        {{ BootstrapForm::close() }}
    </div>
    @endif
    
    <div class="col-md-6">
        <br/>
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
                        <table>
                            <tbody>
                                <tr>
                                    <td style="padding-right: 10px"><strong>@lang('borrower.mails.application-feedback
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stop.borrowerEmail')</strong></td>
                                    <td>{{ $feedbackMessage->getBorrowerEmail() }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('borrower.mails.application-feedback
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stop.replyTo')</strong></td>
                                    <td>{{ $feedbackMessage->getReplyTo() }}</td>
                                </tr>
                                @if($feedbackMessage->getCc())
                                <tr>
                                    <td style="vertical-align: top"><strong>@lang('borrower.mails.application-feedback
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stop.cc')</strong></td>
                                    <td>
                                        @foreach($feedbackMessage->getCcEmails() as $ccEmail)
                                        {{ $ccEmail }}<br/>
                                        @endforeach
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>@lang('borrower.mails.application-feedback
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stop.sender')</strong></td>
                                    <td>{{ $feedbackMessage->getSenderName() }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <br/>
                        <p>
                            {{ nl2br($feedbackMessage->getMessage()) }}                            
                        </p>                        
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
