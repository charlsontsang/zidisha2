@extends('layouts.master')

@section('page-title')
Join the global P2P microlending movement
@stop

@section('content')
<div class="page-header">
    <h1>Borrower Details</h1>
</div>

<img src="{{ $borrower->getUser()->getProfilePictureUrl() }}">

<p><strong>Username: </strong> {{ $borrower->getUser()->getUsername() }} </p> <br>

<p><strong>About me: </strong> {{ $borrower->getProfile()->getAboutMe() }} </p> <br>

<p><strong>About business: </strong> {{ $borrower->getProfile()->getAboutBusiness() }} </p> <br>

    @if(!$borrower->getUploads()->isEmpty())
        <h4>Borrower Pictures</h4>
        <div>
            @foreach($borrower->getUploads() as $upload)
                @if($upload->isImage())
                    <a href="{{ $upload->getImageUrl('small-profile-picture') }}">
                        <img src="{{ $upload->getImageUrl('small-profile-picture') }}" alt=""/>
                    </a>
                @else
                    <div class="well">
                        <a href="{{  $upload->getFileUrl()  }}">{{ $upload->getFilename() }}</a>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
@stop
