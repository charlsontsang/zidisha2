@extends('layouts.master')

@section('page-title')
    @lang('borrow.page-title')
@stop

@section('content')

<h2>{{ \Lang::get('borrower.borrow.information-heading') }}</h2>
<div class="row">
    <div class="col-md-6">
    <p>{{ \Lang::get('borrower.borrow.information-content-part1') }}</p>
    </div>

    <div class="col-md-6">
        <p>{{ \Lang::get('borrower.borrow.information-content-part2') }}</p>
    </div>
</div>

<h2>{{ \Lang::get('borrower.borrow.requirements-heading') }}</h2>
<div class="row">
    <div class="col-md-4">
        <p>{{ \Lang::get('borrower.borrow.requirements-content-facebook') }}</p>
    </div>

    <div class="col-md-4">
        <p>{{ \Lang::get('borrower.borrow.requirements-content-business') }}</p>
    </div>

    <div class="col-md-4">
        <p>{{ \Lang::get('borrower.borrow.requirements-content-leader') }}</p>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <h3>{{ \Lang::get('borrower.borrow.how-much-heading') }}</h3>
        <p>{{ \Lang::get('borrower.borrow.how-much-content') }}</p>
        <p>{{ \Lang::get('borrower.borrow.how-much-max-loan') }}</p>
        <p>{{ \Lang::get('borrower.borrow.how-much-loan-steps') }}</p>
    </div>

    <div class="col-md-6">
        <h3>{{ \Lang::get('borrower.borrow.fees-heading') }}</h3>
        <p>{{ \Lang::get('borrower.borrow.fees-content-part1') }}</p>
        <p>{{ \Lang::get('borrower.borrow.fees-content-part2') }}</p>
        <p>{{ \Lang::get('borrower.borrow.fees-content-part3') }}</p>

        <h3>{{ \Lang::get('borrower.borrow.how-do-heading') }}</h3>
        <p>{{ \Lang::get('borrower.borrow.how-do-content') }}</p>
        <a href="{{ route('borrower:join') }}" class="btn btn-primary">
            {{ \Lang::get('borrower.borrow.how-do-apply') }}
        </a>
    </div>
</div>
<div class="row">
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
@stop
