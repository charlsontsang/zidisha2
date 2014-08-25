@extends('layouts.master')

@section('page-title')
Borrowers
@stop

@section('content')

<div>
    {{ BootstrapForm::open(array('route' => 'admin:borrowers', 'translationDomain' => 'borrowers', 'method' => 'get')) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::select('country', $form->getCountries(), Request::query('country')) }}
    {{ BootstrapForm::select('status', $form->getStatus(), Request::query('status')) }}
    {{ BootstrapForm::text('searchInput', Request::query('searchInput')) }}
    {{ BootstrapForm::submit('Search') }}

    {{ BootstrapForm::close() }}
</div>

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
{{ BootstrapHtml::paginator($paginator)->appends(['country' => Request::query('country'), 'searchInput' => Request::query('searchInput'), 'status' => Request::query('status')])->links() }}
@stop
