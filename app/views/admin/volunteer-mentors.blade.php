@extends('layouts.master')

@section('page-title')
Volunteer Mentors
@stop

@section('content')
<div class="page-header">
    <h1>Volunteer Mentors</h1>
</div>
<div>
    {{ BootstrapForm::open(array('route' => 'admin:volunteer-mentors', 'translationDomain' => 'volunteer-mentors', 'method' => 'get')) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::select('country', $form->getCountries(), Request::query('country')) }}
    {{ BootstrapForm::text('search', Request::query('search')) }}
    {{ BootstrapForm::submit('Search') }}

    {{ BootstrapForm::close() }}
</div>

<table class="table table-striped">
    <thead>
    <tr>
        <th>Borrower</th>
        <th>City / Village</th>
        <th>Number of Assigned Members</th>
        <th>Assigned Member Status</th>
        <th>VM Repayment Status</th>
        <th>Notes</th>
        <th>Volunteer Mentor Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($paginator as $borrower)
    <tr>
        <td><a href="{{ route('lender:public-profile', $borrower->getUser()->getUserName()) }}">{{
                $borrower->getFirstName() }} {{ $borrower->getLastName() }}</a>
            <p>{{ $borrower->getProfile()->getPhoneNumber() }}</p>
            <p>{{ $borrower->getUser()->getEmail() }}</p>
        </td>
        <td>{{ $borrower->getProfile()->getCity() }}</td>
        <td>{{ $borrower->getUser()->getJoinedAt()->format('M j, Y') }}</td>
        <td>
            <a href="{{ route('admin:remove:volunteer-mentor', $borrower->getId()) }}">Remove Volunteer Mentor</a>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->appends(['country' => Request::query('country'), 'search' => Request::query('search')])->links() }}
@stop
