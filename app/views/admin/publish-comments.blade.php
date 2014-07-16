@extends('layouts.master')

@section('page-title')
Publish Comments
@stop

@section('content')
    <div class="page-header">
        <h1>
            Unpublished Comments
        </h1>
    </div>

    <div class="row">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Message</th>
                    <th>User</th>
                    <th>Publish</th>
                </tr>
            </thead>
            <tbody>
                @foreach($comments as $comment)
                <tr>
                    <td>
                        <p>
                            {{ $comment->getMessage() }}
                        </p>
                        @if(!$comment->isOrphanDeleted())
                            @if(\Auth::user() == $comment->getUser())
                                <div class="comment-uploads">
                                    @foreach($comment->getUploads() as $upload)
                                        @if($upload->isImage())
                                            <div class="comment-form" data-comment-action="delete-upload">
                                                <a href="{{ $upload->getImageUrl('small-profile-picture') }}">
                                                    <img src="{{ $upload->getImageUrl('small-profile-picture') }}" width="100px" height="100px" alt=""/>
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
                            @endif
                        @endif
                    </td>
                    <td>
                        <h4>{{ $comment->getUser()->getUserName() }}</h4>
                        @if($comment->getUser() && !$comment->getUser()->isAdmin())
                            <a class="pull-left" href="{{ $comment->getUser()->getProfileUrl() }}">
                                <img class="media-object" width="100px" height="100px" src="{{ $comment->getUser()->getProfilePictureUrl() }}" alt="">
                            </a>
                        @else
                            <a class="pull-left">
                                <img class="media-object" width="100px" height="100px" src="{{ asset('/assets/images/default.jpg') }}" alt="">
                            </a>
                        @endif
                    </td>
                    <td>
                        {{ BootstrapForm::open(array('route' => 'admin:post:moderate-comments', 'translationDomain' => 'admin.publish-comments')) }}
                        {{ BootstrapForm::hidden('borrowerCommentId', $comment->getId()) }}
                        {{ BootstrapForm::submit('publish') }}
                        {{ BootstrapForm::close() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ BootstrapHtml::paginator($comments)->links() }}
    </div>
@stop
