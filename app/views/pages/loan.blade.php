@extends('layouts.master')

@section('page-title')
    @lang('loan.page-title')
@stop

@section('content')
<div class="page-header">
    <h1>{{ $loan->getSummary()}}</h1>
</div>

<div class="row">
    <div class="col-xs-8">
        <h3>My Story</h3>
        <p>{{ $borrower->getProfile()->getAboutMe() }}</p>
        <h3>About My Business</h3>
        <p>{{ $borrower->getProfile()->getAboutBusiness() }}</p>
        <h3>My Loan Proposal</h3>
        <p>{{ $loan->getDescription() }}</p>
    </div>
    <div class="col-xs-4">
        <h2>{{ $borrower->getFirstName() }} {{ $borrower->getLastName() }}</h2>
        <h4>{{ $borrower->getCountry()->getName() }}</h4>
        <strong>Amount Requested: </strong> USD {{ $loan->getAmount() }}
    </div>
</div>

@stop
