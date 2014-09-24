@extends('layouts.master')

@section('page-title')
Add or Edit Translations
@stop

@section('content')
<h1 class="page-title">
    Add or Edit Translations
</h1>
{{ BootstrapForm::open(['route' => ['admin:post-translate', $loan->getId()]]) }}
{{ BootstrapForm::populate($form) }}

<div class="panel panel-info">
    <div class="panel-heading">
    	<h4>About Me</h4>
    </div>
    <div class="panel-body">
		{{ BootstrapForm::textarea('translateAboutMe', $borrower->getProfile()->getAboutMeTranslation(),
		['description' => $borrower->getProfile()->getAboutMe(), 'id' => 'about-me', 'label' => ''] )}}
	</div>
</div>

<div class="panel panel-info">
    <div class="panel-heading">
    	<h4>About My Business</h4>
    </div>
    <div class="panel-body">
		{{ BootstrapForm::textarea('translateAboutBusiness', $borrower->getProfile()->getAboutBusinessTranslation(),
		['description' => $borrower->getProfile()->getAboutBusiness(), 'id' => 'about-business', 'label' => ''] )}}
	</div>
</div>

<div class="panel panel-info">
    <div class="panel-heading">
    	<h4>My Loan Proposal</h4>
    </div>
    <div class="panel-body">
		{{ BootstrapForm::textarea('translateProposal', $loan->getProposalTranslation(), ['description' => $loan->getProposal(),
		'id' => 'proposal', 'label' => ''] )}}
	</div>
</div>
{{ BootstrapForm::submit('Publish translations') }}

{{ BootstrapForm::close() }}

<br/>
<p><a href="{{ route('loan:index', $loan->getId()) }}">Cancel</a></p>

@stop
