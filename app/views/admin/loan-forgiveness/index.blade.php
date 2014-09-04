@extends('layouts.side-menu')

@section('page-title')
Forgiven Loans
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.staff-links')
@stop

@section('page-content')
<a href="{{route('admin:loan-forgiveness:allow', $countryCode)}}">Enable Loan Forgiveness</a>
<hr/>

<ul class="nav nav-tabs" role="tablist">
    @foreach($borrowerCountries as $borrowerCountry)
        <li class="{{ $borrowerCountry->getCountryCode() == $countryCode ? 'active' : '' }}">
            <a href="{{ route('admin:loan-forgiveness:index', $borrowerCountry->getCountryCode()) }}">
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
            <th>Detail</th>
        </tr>
    </thead>
    <tbody>
        @foreach($forgivenessLoans as $loan)
            <tr>
                <td>
                     {{ $loan->getBorrower()->getFirstName() }} {{ $loan->getBorrower()->getLastName() }}
                </td>
                <td>
                    {{ $loan->getLoanId() }}
                </td>
                <td>
                   <p>
                        {{{ $loan->getComment() }}}
                   </p> 
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

{{ BootstrapHtml::paginator($forgivenessLoans)->links() }}
@stop

