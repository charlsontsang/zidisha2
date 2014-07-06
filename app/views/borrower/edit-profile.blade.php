@extends('layouts.master')

@section('page-title')
Join the global P2P microlending movement
@stop

@section('content')
<div class="page-header">
    <h1>Edit Profile</h1>
</div>
<hr/>
<div class="borrower-edit-form">
    <h3>Current Profile Picture</h3>
    <img src="{{ $borrower->getUser()->getProfilePictureUrl() }}" alt=""/>
    {{ BootstrapForm::open(array('route' => 'borrower:post-profile', 'translationDomain' => 'borrower.edit-profile', 'files' => true)) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::file('picture') }}

    {{ BootstrapForm::password('password') }}

    {{ BootstrapForm::password('password_confirmation') }}

    {{ BootstrapForm::text('email') }}

    {{ BootstrapForm::textarea('aboutMe') }}

    {{ BootstrapForm::textarea('aboutBusiness') }}

    <div class="borrower-upload-inputs">
        {{ BootstrapForm::file('images[]', ['label' => 'borrower.edit-profile.upload-file']) }}
        <button class="btn btn-primary btn-success borrower-upload-add-more">@lang('borrower.add-more')</button>
    </div>

    {{ BootstrapForm::submit('save') }}

    {{ BootstrapForm::close() }}

    @if(!$borrower->getUploads()->isEmpty())
    <h4>Borrower Pictures</h4>
    <div>
        @foreach($borrower->getUploads() as $upload)

                <div class="borrower-upload-form" data-comment-action="delete-upload">
                    {{ BootstrapForm::open(array('route' => 'borrower:delete-upload', 'translationDomain' => 'borrower.edit-uploads')) }}
                    @if($upload->isImage())
                        <a href="{{ $upload->getImageUrl('small-profile-picture') }}">
                            <img src="{{ $upload->getImageUrl('small-profile-picture') }}" width="100px" height="100px" alt=""/>
                        </a>
                    @else
                        <div class="well">
                            <a href="{{  $upload->getFileUrl()  }}">{{ $upload->getFilename() }}</a>
                        </div>
                    @endif
                    {{ BootstrapForm::hidden('borrower_id', $borrower->getId()) }}
                    {{ BootstrapForm::hidden('upload_id', $upload->getId()) }}
                    {{ BootstrapForm::submit('delete') }}
                    {{ BootstrapForm::close() }}
                </div>

        @endforeach
    </div>
    @endif
</div>

<script type="text/html" id="borrower-upload-input-template">
    {{ BootstrapForm::file('images[]', ['label' => 'borrower.edit-profile.upload-file']) }}
</script>

@stop
