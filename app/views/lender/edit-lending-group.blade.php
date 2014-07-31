@extends('layouts.master')

@section('page-title')
Edit Lending Group
@stop

@section('content')
{{ BootstrapForm::open(['route' => ['lender:groups:post-edit', $group->getId()], 'translationDomain' => 'lender.group', 'files' => true]) }}

{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::text('name') }}

{{ BootstrapForm::text('website') }}

{{ BootstrapForm::file('groupProfilePictureId') }}
@if($group->getGroupProfilePicture())
    <img src="{{ $group->getGroupProfilePicture()->getImageUrl('small-profile-picture') }}" alt=""/>
@endif

{{ BootstrapForm::textarea('about') }}

{{ BootstrapForm::select('userId', $form->getMembers(), $group->getLeader()->getUser()->getId()) }}

{{ BootstrapForm::submit('save') }}

{{ BootstrapForm::close() }}

@stop
