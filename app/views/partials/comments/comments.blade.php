<div class="comments">
    
    <div class="row">
        <div class="col-sm-10 col-sm-push-2">
            @if($canPostComment)
                @if(\Auth::check())
                    @include('partials.comments.partial.post', [
                        'controller' => $controller,
                        'receiver' => $receiver,
                        'canPostComment' => $canPostComment,
                        'canReplyComment' => $canReplyComment
                    ])
                @else
                    <span class="text-light">
                        @lang('common.comments.login', ['link' => route('login')])
                    </span>
                @endif
            @endif
        </div>
    </div>

    <ul class="media-list">
        @foreach($comments as $comment)
            @include("partials.comments.root", [
                'comment' => $comment,
                'controller' => $controller,
                'receiver' => $receiver,
                'canPostComment' => $canPostComment,
                'canReplyComment' => $canReplyComment
            ])
        @endforeach
    </ul>
    {{ BootstrapHtml::paginator($comments)->links() }}
    
    <script type="text/html" id="comment-upload-input-template">
        <div class="file-input-block">
            <div style="display: inline-block;">
                {{ BootstrapForm::file('file[]', ['label' => 'common.comments.
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stopupload-file', 'class' => 'upload-file']) }}
>>>>>>> a157f98... move comment labels from borrower to common directory
            </div>
            <div style="display: inline-block;">
                <a href="#" data-dismiss='removeFile'>
                    <i class="fa fa-times"></i>    
                </a>
            </div>
        </div>
    </script>
</div>
