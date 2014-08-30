@extends('layouts.master')

@section('page-title')
Volunteers
@stop

@section('content')
<div class="page-header">
    <h1>Volunteers</h1>
</div>
<div>
    {{ BootstrapForm::open(array('route' => 'admin:volunteers', 'translationDomain' => 'volunteers', 'method' => 'get')) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::select('country', $form->getCountries(), Request::query('country')) }}
    {{ BootstrapForm::text('search', Request::query('search')) }}
    {{ BootstrapForm::submit('Search') }}

    {{ BootstrapForm::close() }}
</div>

<table class="table table-striped">
    <thead>
    <tr>
        <th>Lender</th>
        <th>Location</th>
        <th>Date Joined</th>
        <th>Volunteer Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($paginator as $lender)
    <tr>
        <td><a href="{{ route('lender:public-profile', $lender->getUser()->getUserName()) }}">{{
                $lender->getFirstName() }} {{ $lender->getLastName() }}</a>
            <p>{{ $lender->getUser()->getUsername() }}</p>
            <p>{{ $lender->getUser()->getEmail() }}</p>
        </td>
        <td>{{ $lender->getCountry()->getName() }}</td>
        <td>{{ $lender->getUser()->getJoinedAt()->format('M j, Y') }}</td>
        <td>
            @if($lender->getUser()->getSubRole() == \Zidisha\User\User::SUB_ROLE_VOLUNTEER)
                <a href="{{ route('admin:remove:volunteer', $lender->getId()) }}">Remove Volunteer</a>
            @else
                <a href="{{ route('admin:add:volunteer', $lender->getId()) }}">Add Volunteer</a>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->appends(['country' => Request::query('country'), 'search' => Request::query('search')])->links() }}
@stop
