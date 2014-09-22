@extends('layouts.side-menu-simple')

@section('page-title')
Edit Profile
@stop

@section('menu-title')
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stop

@section('page-content')
<div class="borrower-edit-form">

    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">
                @lang('borrower.loan-application.profile.your-pictures')
            </h3>
        </div>
        <div class="panel-body">
            @if(!$borrower->getUploads()->isEmpty())
            <div>
                @foreach($borrower->getUploads() as $upload)

                        <div class="borrower-upload-form" data-comment-action="delete-upload">
                            {{ BootstrapForm::open(array('route' => 'borrower:delete-upload', 'translationDomain' => 'borrower.edit-uploads')) }}
                            @if($upload->isImage())
                                <a href="{{ $upload->getImageUrl('small-profile-picture', true) }}">
                                    <img src="{{ $upload->getImageUrl('small-profile-picture', true) }}" width="100px" height="100px" alt=""/>
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

            <img src="{{ $borrower->getUser()->getProfilePictureUrl() }}" alt=""/>

            {{ BootstrapForm::open(array('route' => 'borrower:post-profile', 'translationDomain' => 'borrower.loan-application.profile', 'files' => true)) }}
            
            {{ BootstrapForm::populate($form) }}

            {{ BootstrapForm::file('picture') }}

            <div class="borrower-upload-inputs">
                {{ BootstrapForm::file('morePictures') }}
            </div>

        </div>
    </div>

    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">
                @lang('borrower.loan-application.profile.your-intro')
            </h3>
        </div>
        <div class="panel-body">

            {{ BootstrapForm::textarea('aboutMe') }}

            {{ BootstrapForm::textarea('aboutBusiness') }}

        </div>
    </div>

    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">
                @lang('borrower.loan-application.profile.your-account')
            </h3>
        </div>
        <div class="panel-body">

            {{ BootstrapForm::password('changePassword') }}

            {{ BootstrapForm::password('confirmChangePassword') }}

            {{ BootstrapForm::text('changeEmail') }}

        </div>
    </div>

    {{ BootstrapForm::submit('save') }}

    {{ BootstrapForm::close() }}

</div>

<script type="text/html" id="borrower-upload-input-template">
    {{ BootstrapForm::file('images[]', ['label' => 'borrower.edit-profile.upload-file']) }}
</script>

@stop
