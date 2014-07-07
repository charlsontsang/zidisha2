@extends('layouts.master')

@section('page-title')
Translate
@stop

@section('content')

<a href="{{ route('loan:index', $loan->getId()) }}">back to Loan</a>
{{ BootstrapForm::open(['route' => ['admin:post-translate', $loan->getId()]]) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::textarea('translateAboutMe', $borrower->getProfile()->getAboutMeTranslation(),
['description' => $borrower->getProfile()->getAboutMe(), 'id' => 'about-me'] )}}

{{ BootstrapForm::textarea('translateAboutBusiness', $borrower->getProfile()->getAboutBusinessTranslation(),
['description' => $borrower->getProfile()->getAboutBusiness(), 'id' => 'about-business'] )}}

{{ BootstrapForm::textarea('proposal', $loan->getProposal(), array('readonly')) }}

{{ BootstrapForm::textarea('translateProposal', $loan->getProposalTranslation(), ['description' => $loan->getProposal(),
'id' => 'proposal'] )}}

{{ BootstrapForm::submit('save') }}

{{ BootstrapForm::close() }}
@stop
