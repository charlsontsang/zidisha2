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
{{ BootstrapForm::open(array('route' => 'admin:borrowers', 'translationDomain' => 'borrowers', 'method' => 'get')) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::select('country', $form->getCountries(), Request::query('country')) }}
{{ BootstrapForm::select('status', $form->getStatus(), Request::query('status')) }}
{{ BootstrapForm::text('search', Request::query('search')) }}
{{ BootstrapForm::submit('Search') }}

{{ BootstrapForm::close() }}

<table class="table table-striped">
    <thead>
    <tr>
        <th>Borrower</th>
        <th>Location</th>
        <th>To Do</th>
        <th></th>
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
        <td>{{ $borrower->getCountry()->getName() }}</td>
        <td></td>
        <td>
            <a href="{{ route('admin:borrower', $borrower->getId()) }}">
                <i class="fa fa-info-circle fa-lg"></i>
            </a>
            <a href="{{ route('admin:borrower:edit', $borrower->getId()) }}">
                <i class="fa fa-pencil-square-o fa-lg"></i>
            </a>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->appends(['country' => Request::query('country'), 'search' => Request::query('search'), 'status' => Request::query('status')])->links() }}
@stop
