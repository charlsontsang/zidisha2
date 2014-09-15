<div class="comment-form clearfix" style="display: none;" data-comment-action="translate" >
    {{ BootstrapForm::open(array('action' => [ $controller.'@postTranslate', 'id' => $receiver->getId()  ], 'translationDomain' => 'common.comments')) }}

    {{ BootstrapForm::textarea('message', $comment->getMessageTranslation(), ['label' => 'common.comments.
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stoptranslation', 'required' => 'required', 'rows' => 5]) }}
    {{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
    
    <div class="pull-right">
        {{ BootstrapForm::submit('actions.translate', ['data-loading-text' => \Lang::get('common.comments.
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stoploading-text.translate')]) }}
    </div>

    {{ BootstrapForm::close() }}
</div>
