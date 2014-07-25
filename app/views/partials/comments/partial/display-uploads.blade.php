@if(!$comment->isOrphanDeleted())
    @if(\Auth::user() == $comment->getUser())
        <div class="comment-uploads">
            @foreach($comment->getUploads() as $upload)
                @if($upload->isImage())
                    <div class="comment-form" data-comment-action="delete-upload">
                        {{ BootstrapForm::open(array('action' => [ $controller.'@postDeleteUpload', 'id' => $receiver->getId()  ], 'translationDomain' => 'comments')) }}

                        <a href="{{ $upload->getImageUrl('small-profile-picture') }}">
                            <img src="{{ $upload->getImageUrl('small-profile-picture') }}" width="100px" height="100px" alt=""/>
                        </a>

                        {{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
                        {{ BootstrapForm::hidden('upload_id', $upload->getId()) }}
                        {{ BootstrapForm::submit('delete') }}
                        {{ BootstrapForm::close() }}
                    </div>
                @else
                    <div class="comment-form" data-comment-action="delete-upload">
                        {{ BootstrapForm::open(array('action' => [ $controller.'@postDeleteUpload', 'id' => $receiver->getId()  ], 'translationDomain' => 'comments')) }}

                        <div class="well">
                            <a href="{{  $upload->getFileUrl()  }}">{{ $upload->getFilename() }}</a>
                        </div>

                        {{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
                        {{ BootstrapForm::hidden('upload_id', $upload->getId()) }}
                        {{ BootstrapForm::submit('delete', ['data-submit' => '', 'data-loading-text' => \Lang::get('comments.delete.loading-text')]) }}
                        {{ BootstrapForm::close() }}
                    </div>
                @endif
            @endforeach
        </div>
    @endif
@endif
