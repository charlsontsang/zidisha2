@extends('layouts.master')

@section('page-title')
Countries
@stop

@section('content')
<div class="page-header">
    <h1>Countries</h1>
</div>

<ul class="nav nav-tabs" role="tablist">
    <li class="{{ $otherCountries ? '' : 'active' }}"><a href="{{ route('admin:countries') }}">Borrower Countries</a></li>
    <li class="{{ !$otherCountries ? '' : 'active' }}"><a href="{{ route('admin:countries') . '?other_countries=true' }}">Other Countries</a></li>
</ul>
<div class="panel panel-info">
    <div class="panel-body">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Country Name</th>
                <th>Dialing Code</th>
                <th>Registration Fee</th>
                <th>Installment Period</th>
                <th>Edit</th>
            </tr>
            </thead>
            <tbody>
            @foreach($countries as $country)
            <tr>
                <td><a href="{{ route('admin:edit:country', $country->getId()) }}"> {{ $country->getName() }} </a></td>
                <td>{{ $country->getDialingCode() }}</td>
                <td>{{ $country->getRegistrationFee() }}</td>
                <td>{{ $country->getInstallmentPeriod() }}</td>
                @if(Auth::getUser()->isAdmin())
                    <td>
                        <a href="{{ route('admin:edit:country', $country->getId()) }}">
                            <i class="fa fa-pencil-square-o fa-lg"></i>
                        </a>
                    </td>
                @endif
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
