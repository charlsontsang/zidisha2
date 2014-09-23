@extends('layouts.master')

@section('page-title')
Borrower Refunds
@stop

@section('content')
<div class="page-header">
    <h1> Borrower Refunds </h1>
</div>
<br/>
{{ BootstrapForm::open(array('route' => 'admin:post-repayments-refunds', 'translationDomain' => 'borrower-refunds',
'id' => 'borrower-refunds')) }}
<button id="borrower-refunds" class="btn btn-primary" type="submit"> Mark Selected As Refunded </button>
<br/>
<br/>
<table class="table table-striped">
    <thead>
    <tr>
        <th> //TODO select all</th>
        <th>Created</th>
        <th>Country</th>
        <th>Borrower</th>
        <th>Loan</th>
        <th>Amount</th>
    </tr>
    </thead>
    <tbody>
    @foreach($refunds as $refund)
    <tr>
        <td>{{ BootstrapForm::checkbox('refundsIds[]', $refund->getId()) }}</td>
        <td>{{ $refund->getCreatedAt()->format('d-m-Y') }}</td>
        <td>{{ $refund->getBorrower()->getCountry()->getName() }}</td>
        <td>{{ $refund->getBorrower()->getName() }}</td>
        <td>{{ $refund->getLoan()->getSummary() }}</td>
        <td>{{ $refund->getAmount() }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ BootstrapForm::close() }}
@stop
