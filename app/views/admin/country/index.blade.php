@extends('layouts.master')

@section('page-title')
Countries
@stop

@section('content')
<h1>Countries</h1>
<hr/>
    <ul class="nav nav-tabs" role="tablist">
        <li class="{{ $otherCountries ? '' : 'active' }}"><a href="{{ route('admin:countries') }}">Borrower Countries</a></li>
        <li class="{{ !$otherCountries ? '' : 'active' }}"><a href="{{ route('admin:countries') . '?other_countries=true' }}">Other Countries</a></li>
    </ul>

<table class="table table-striped">
    <thead>
    <tr>
        <th>Country Name</th>
        <th>Dialing Code</th>
        <th>Registration Fee</th>
        <th>Installment Period</th>
    </tr>
    </thead>
    <tbody>
    @foreach($countries as $country)
    <tr>
        <td><a href="{{ route('admin:edit:country', $country->getId()) }}"> {{ $country->getName() }} </a></td>
        <td>{{ $country->getDialingCode() }}</td>
        <td>{{ $country->getRegistrationFee() }}</td>
        <td>{{ $country->getInstallmentPeriod() }}</td>
        <td>
            <a href="{{ route('admin:edit:country', $country->getId()) }}">
                <i class="fa fa-pencil-square-o fa-lg"></i>
            </a>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
@stop
