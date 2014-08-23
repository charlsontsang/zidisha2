@extends('layouts.master')

@section('page-title')
Forgiven Loans
@stop

@section('content')
<div class="page-header">
    <h1>Forgiven Loans</h1>
</div>

<ul class="nav nav-tabs" role="tablist">
    @foreach($borrowerCountries as $borrowerCountry)
        <li class="{{ $borrowerCountry->getCountryCode() == $countryCode ? 'active' : '' }}">
            <a href="{{ route('admin:forgiven-loan:index') }}?countryCode={{ $borrowerCountry->getCountryCode() }} ">
                {{ $borrowerCountry->getName() }}
            </a>
        </li>
    @endforeach
</ul>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Borrower</th>
            <th>Loan Id</th>
            <th>Comment</th>
            <th>Date</th>
            <th>detail</th>
        </tr>
    </thead>
    <tbody>
        @foreach($forgivenLoans as $loan)
            <tr>
                <td>
                     {{ $loan->getBorrower()->getFirstName() }} {{ $loan->getBorrower()->getLastName() }}
                </td>
                <td>
                    {{ $loan->getLoanId() }}
                </td>
                <td>
                    //TODO comment
                </td>
                <td>
                    {{ $loan->getCreatedAt()->format('M DD, YY') }}
                </td>
                <td>
                    //TODO
                </td>
            </tr>
        @endforeach    
    </tbody>
</table>

{{ BootstrapHtml::paginator($forgivenLoans)->links() }}
@stop
