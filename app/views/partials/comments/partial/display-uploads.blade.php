@if(!$comment->isOrphanDeleted())
    @if(\Auth::user() == $comment->getUser())
        <div class="comment-uploads">
            @foreach($comment->getUploads() as $upload)
                @if($upload->isImage())
                    <div class="comment-form" data-comment-action="delete-upload">
                        {{ BootstrapForm::open(array('route' => 'comment:delete-upload', 'translationDomain' => 'comments')) }}

                        <a href="{{ $upload->getUrl() }}">
                            <img src="{{ $upload->getUrl() }}" width="100px" height="100px" alt=""/>
                        </a>

                        {{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
                        {{ BootstrapForm::hidden('upload_id', $upload->getId()) }}
                        {{ BootstrapForm::submit('delete') }}
                        {{ BootstrapForm::close() }}
                    </div>
                @else
                    <div class="comment-form" data-comment-action="delete-upload">
                        {{ BootstrapForm::open(array('route' => 'comment:delete-upload', 'translationDomain' => 'comments')) }}

                        <div class="well">
                            <a href="{{  $upload->getUrl()  }}">{{ $upload->getFilename() }}</a>
                        </div>

                        {{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
                        {{ BootstrapForm::hidden('upload_id', $upload->getId()) }}
                        {{ BootstrapForm::submit('delete') }}
                        {{ BootstrapForm::close() }}
                    </div>
                @endif
            @endforeach
        </div>
    @endif
@endif