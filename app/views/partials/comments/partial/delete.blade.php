<div class="comment-form" style="display: none;" data-comment-action="delete" >
    {{ BootstrapForm::open(array('action' => [ $controller.'@postDelete', 'id' => $receiver->getId() ], 'translationDomain' => 'borrower.comments')) }}
    {{ BootstrapForm::label(\Lang::get('common.comments.
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stopdelete-comment')) }}
    {{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
    {{ BootstrapForm::submit('actions.delete', ['class' => 'btn btn-danger', 'data-loading-text' => \Lang::get('common.comments.
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stoploading-text.delete')]) }}
    {{ BootstrapForm::close() }}
</div>
