@extends('layouts.side-menu')

@section('page-title')
Find Loans
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.staff-links')
@stop

@section('page-content')
{{ BootstrapForm::open(array('route' => 'admin:loans', 'translationDomain' => 'loans', 'method' => 'get')) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::select('country', $form->getCountries(), Request::query('country')) }}
{{ BootstrapForm::select('status', $form->getStatus(), Request::query('status')) }}
{{ BootstrapForm::submit('save') }}

{{ BootstrapForm::close() }}

<table class="table table-striped">
    <thead>
    <tr>
        <th>Loan</th>
        <th>Status</th>
        <th>Borrower</th>
    </tr>
    </thead>
    <tbody>
    @foreach($paginator as $loan)
    <tr>
        <td><a href="{{ route('loan:index', $loan->getId()) }}">{{
                $loan->getSummary() }}</a>

            <p>{{ $loan->getUsdAmount() }} USD</p>

            <p>{{ Request::query('status')?: 'Fund Raising' }}</p>
        </td>
        <td>@include('partials/_progress', [ 'raised' => rand(1,100)])</td>
        <td>
            <p><a href="{{ route('admin:borrower:personal-information', $loan->getBorrower()->getUser()->getUsername()) }}">
               {{ $loan->getBorrower()->getFirstName() }} {{ $loan->getBorrower()->getLastName() }}
               </a></p>
            <p>{{ $loan->getBorrower()->getUser()->getUsername() }}</p>
            <p>{{ $loan->getBorrower()->getUser()->getEmail() }}</p>
            <p>{{ $loan->getBorrower()->getCountry()->getName() }}</p>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ $paginator->appends(['country' => Request::query('country'), 'status' => Request::query('status')])->links() }}
@stop

