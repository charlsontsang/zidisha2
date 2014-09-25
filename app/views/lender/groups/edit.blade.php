@extends('layouts.master')

@section('page-title')
Edit Lending Group
@stop

@section('content')
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="highlight highlight-panel">
			<div class="page-header">
			    <h1>Edit Lending Group</h1>
			</div>
			{{ BootstrapForm::open(['route' => ['lender:groups:post-edit', $group->getId()], 'translationDomain' => 'lender.shared-labels.groups', 'files' => true]) }}

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
</div>
@stop
