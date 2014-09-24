@extends('layouts.master')

@section('page-title')
Start a New Lending Group
@stop

@section('content')
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="highlight highlight-panel">
			<h2>Start a New Lending Group</h2>

			{{ BootstrapForm::open(array('route' => 'lender:groups:post-create', 'translationDomain' => 'lender.group', 'files' => true)) }}
			{{ BootstrapForm::populate($form) }}

			{{ BootstrapForm::text('name') }}

			{{ BootstrapForm::text('website') }}

			{{ BootstrapForm::file('groupProfilePictureId') }}

			{{ BootstrapForm::textarea('about') }}

			{{ BootstrapForm::submit('save') }}

			{{ BootstrapForm::close() }}
		</div>
	</div>
</div>
@stop
