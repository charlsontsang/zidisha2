@extends('layouts.master')

@section('page-title')
Edit Lending Group
@stop

@section('content')
<div class="row">
	<div class="col-sm-6 col-sm-offset-3">
		<div class="page-header">
		    <h1>Edit Lending Group</h1>
		</div>
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
	</div>
</div>
@stop
