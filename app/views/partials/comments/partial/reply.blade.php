<div class="comment-form clearfix" data-comment-action="reply" style="display: none;">
    {{ BootstrapForm::open(array('action' => [ $controller.'@postReply', 'id' => $receiver->getId()  ], 'translationDomain' => 'borrower.comments', 'files' => true)) }}

    {{ BootstrapForm::textarea('message', null, ['required' => 'required', 'rows' => 5]) }}
    {{ BootstrapForm::hidden('receiver_id', $receiver->getId()) }}
    {{ BootstrapForm::hidden('parent_id', $comment->getId()) }}

    @if($controller != 'LoanFeedbackController')
        <a href="#" data-display='display' target='.reply-comment-upload-inputs'>
            <i class="fa fa-camera"></i> @lang('common.comments.
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stopadd-photo')
        </a>
        <div class="comment-upload-inputs reply-comment-upload-inputs" style="display: none;">
            {{ BootstrapForm::file('file[]', ['label' => 'common.comments.
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stopupload-file']) }}
            <button class="btn btn-primary btn-success comment-upload-add-more">@lang('common.comments.
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stopadd-more')</button>
        </div>
    @endif
    
    <div class="pull-right">
        {{ BootstrapForm::submit('actions.reply', ['data-loading-text' => \Lang::get('common.comments.
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stoploading-text.reply')]) }}
    </div>
    
    {{ BootstrapForm::close() }}
</div>
