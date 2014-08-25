@extends('layouts.master')

@section('page-title')
Volunteer Mentors
@stop

@section('content')

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
        <th>Location</th>
        <th>Date Joined</th>
        <th>Volunteer Mentor Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($paginator as $borrower)
    <tr>
        <td><a href="{{ route('lender:public-profile', $borrower->getUser()->getUserName()) }}">{{
                $borrower->getFirstName() }} {{ $borrower->getLastName() }}</a>
            <p>{{ $borrower->getUser()->getUsername() }}</p>
            <p>{{ $borrower->getUser()->getEmail() }}</p>
        </td>
        <td>{{ $borrower->getCountry()->getName() }}</td>
        <td>{{ $borrower->getUser()->getJoinedAt()->format('M j, Y') }}</td>
        <td>
            @if($borrower->getUser()->getSubRole() == \Zidisha\User\User::SUB_ROLE_VOLUNTEER_MENTOR)
                <a href="{{ route('admin:remove:volunteer-mentor', $borrower->getId()) }}">Remove Volunteer Mentor</a>
            @else
                <a href="{{ route('admin:add:volunteer-mentor', $borrower->getId()) }}">Add Volunteer Mentor</a>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->appends(['country' => Request::query('country'), 'search' => Request::query('search')])->links() }}
@stop
