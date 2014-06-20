@extends('layouts.master')

@section('page-title')
    @lang('borrow.page-title')
@stop

@section('content')

<div class="row">
<h2>INFORMATION FOR BORROWERS</h2>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Country</th>
            <th>Fee</th>
        </tr>
        </thead>
        <tbody>
        @foreach($countries as $country)
        <tr>
            <td>{{ $country->getName() }}</td>
            <td>{{ strtoupper($country->getCurrencyCode()) }} {{ $country->getRegistrationFee() }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
<a href="{{ route('borrower:join') }}" class="btn btn-primary">
            Apply
        </a>
@stop
