@extends('layouts.side-menu')

@section('page-title')
Look Up Borrower Account
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.staff-links')
@stop

@section('page-content')
{{ BootstrapForm::open(array('route' => 'admin:borrowers', 'method' => 'get')) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::select('country', $form->getCountries(), Request::query('country'), ['label' => 'Country']) }}
{{ BootstrapForm::select('status', $form->getStatus(), Request::query('status'), ['label' => 'Account Status']) }}
{{ BootstrapForm::text('Search', Request::query('search')) }}
{{ BootstrapForm::submit('Submit') }}

{{ BootstrapForm::close() }}

<table class="table table-striped">
    <thead>
    <tr>
        <th>Borrower</th>
        <th>Location</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach($paginator as $borrower)
    <tr>
        <td><a href="{{ route('admin:borrower', $borrower->getUser()->getId()) }}">{{
                $borrower->getFirstName() }} {{ $borrower->getLastName() }}</a>
            <p>{{ $borrower->getUser()->getUsername() }}</p>
            <p>{{ $borrower->getUser()->getEmail() }}</p>
        </td>
        <td>
            {{ $borrower->getProfile()->getCity() }}, {{ $borrower->getCountry()->getName() }}
        </td>
        <td>
            <a href="{{ route('admin:borrower', $borrower->getId()) }}">
                View Profile
            </a>
            <br/><br/>
            @if(Auth::getUser()->isAdmin())
            <a href="{{ route('admin:borrower:edit', $borrower->getId()) }}">
                Edit Profile
            </a>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->appends(['country' => Request::query('country'), 'search' => Request::query('search'), 'status' => Request::query('status')])->links() }}
@stop
