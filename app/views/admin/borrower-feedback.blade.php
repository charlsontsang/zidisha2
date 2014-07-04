@extends('layouts.master')

@section('page-title')
Feedback
@stop

@section('content')
<h2>Email Applicant</h2>
<br><br>
<p>Following the guidelines at the page <a href="{{ route('page:loan-feature-criteria') }}">How to Have Your Loan
        Featured</a>, come up with one to three things that this applicant can do to make his or her loan profile more attractive
    to lenders. Then use the form below to email the applicant with your suggestions</p>

{{ BootstrapForm::open(array('route' => 'admin:post-loan-feedback', 'translationDomain' => 'admin.feature-feedback')) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::email('borrowerEmail', $borrower->getUser()->getEmail()) }}
{{ BootstrapForm::text('cc') }}
{{ BootstrapForm::email('replyTo') }}
{{ BootstrapForm::text('subject', $form->getSubject()) }}
(Please modify the default text as necessary to indicate what is needed to improve the loan application,
and add your name to the signature line.<br> You may change the language in the footer of this page to display the default
message in French or Indonesian.)
{{ BootstrapForm::textarea('message', $form->getMessage($borrower->getName())) }}
{{ BootstrapForm::text('senderName') }}

{{ BootstrapForm::submit('save') }}

{{ BootstrapForm::close() }}

<br><br>
@if($feedbackMessages != null)
<table class="table table-striped">
    <thead>
    <tr>
        <th>Borrower Name</th>
        <th>Borrower Email</th>
        <th>Subject</th>
        <th>Message</th>
        <th>Sender Name</th>
        <th>Sent at</th>
    </tr>
    </thead>
    <tbody>
    @foreach($feedbackMessages as $feedbackMessage)
    <tr>
        <td>{{ $feedbackMessage->getBorrower()->getName() }}</td>
        <td>{{ $feedbackMessage->getBorrowerEmail() }}</td>
        <td>{{ $feedbackMessage->getSubject() }}</td>
        <td>{{ $feedbackMessage->getMessage() }}</td>
        <td>{{ $feedbackMessage->getSenderName() }}</td>
        <td>{{ $feedbackMessage->getSentAt()->format('d-m-Y') }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
@endif


@stop
