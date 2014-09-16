@if(!$comment->isOrphanDeleted())
    <div class="comment-uploads">
        @foreach($comment->getUploads() as $upload)
            @if($upload->isImage())
                <div class="comment-form" data-comment-action="delete-upload">
                    {{ BootstrapForm::open(array('action' => [ $controller.'@postDeleteUpload', 'id' => $receiver->getId()  ], 'translationDomain' => 'borrower.comments')) }}

                    <a href="{{ $upload->getImageUrl('small-profile-picture') }}">
                        <img src="{{ $upload->getImageUrl('small-profile-picture') }}" style="max-width:100%;" alt=""/>
                    </a>
                    
                    <br/>
                    <br/>

                    @if(\Auth::user() == $comment->getUser())

                    {{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
                    {{ BootstrapForm::hidden('upload_id', $upload->getId()) }}
                    {{ BootstrapForm::submit('delete-upload') }}
                    {{ BootstrapForm::close() }}
                    
                    @endif
                </div>
            @else
                <div class="comment-form" data-comment-action="delete-upload">
                    {{ BootstrapForm::open(array('action' => [ $controller.'@postDeleteUpload', 'id' => $receiver->getId()  ], 'translationDomain' => 'borrower.comments')) }}

                    <div class="well">
                        <a href="{{  $upload->getFileUrl()  }}">{{ $upload->getFilename() }}</a>
                    </div>

                    @if(\Auth::user() == $comment->getUser())
                    
                    {{ BootstrapForm::hidden('comment_id', $comment->getId()) }}
                    {{ BootstrapForm::hidden('upload_id', $upload->getId()) }}
                    {{ BootstrapForm::submit('delete-upload', ['data-submit' => '', 'data-loading-text' => \Lang::get('common.comments.delete.loading-text')]) }}
                    {{ BootstrapForm::close() }}
                    
                    @endif
                </div>
            @endif
        @endforeach
    </div>
@endif
