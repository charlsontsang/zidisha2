@extends('layouts.master')

@section('page-title')
Lenders
@stop

@section('content')

<div>
    {{ BootstrapForm::open(array('route' => 'admin:lenders', 'translationDomain' => 'lenders', 'method' => 'get')) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::select('country', $form->getCountries(), Request::query('country')) }}
    {{ BootstrapForm::text('searchInput', Request::query('searchInput')) }}
    {{ BootstrapForm::submit('Search') }}

    {{ BootstrapForm::close() }}
</div>

<table class="table table-striped">
    <thead>
    <tr>
        <th>Lender</th>
        <th>Location</th>
        <th>To Do</th>
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
        <td></td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->appends(['country' => Request::query('country'), 'searchInput' => Request::query('searchInput')])->links() }}
@stop
