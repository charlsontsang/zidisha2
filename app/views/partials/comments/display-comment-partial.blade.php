<div class="media comments">
    <a class="pull-left" href="#">
        @if($comment->getUser() && !$comment->getUser()->isAdmin())
        <a class="pull-left" href="{{ $comment->getUser()->getProfileUrl() }}">
            <img class="media-object" width="100px" src="{{ $comment->getUser()->getProfilePictureUrl() }}" alt="">
        </a>
        @else
        <a class="pull-left">
            <img class="media-object" width="100px" src="{{ asset('/assets/images/default.jpg') }}" alt="">
        </a>
        @endif
    </a>
    <div class="media-body">
        <h4 class="media-heading">
            <a href="
            @if($comment->getUser()->isLender())
                {{ $comment->getUser()->getProfileUrl() }}
            @else
                {{ route('loan:index', $comment->getUser()->getBorrower()->getActiveLoanId()) }}
            @endif
            "> {{ $comment->getUser()->getUserName() }}  - {{ $comment->getId() }}</a>
            <small>{{ $comment->getCreatedAt('M j, Y') }}</small>
        </h4>

        <p>
            {{ $comment->getMessage() }}
        </p>
        @foreach($comment->getUploads() as $upload)
        @if($upload->isImage())
        <div class="comment-form" data-comment-action="delete-upload">
            <a href="{{ $upload->getImageUrl('comment-picture') }}">
                <img src="{{ $upload->getImageUrl('comment-picture') }}" alt=""/>
            </a>
        </div>
        @else
        <div class="comment-form" data-comment-action="delete-upload">
            <div class="well">
                <a href="{{  $upload->getFileUrl()  }}">{{ $upload->getFilename() }}</a>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</div>
