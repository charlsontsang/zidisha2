@extends('layouts.master')

@section('page-title')
Repayment Process
@stop

@section('content')
<div class="page-header">
    <h1>Borrower Payments: {{ $name }}</h1>
</div>

@if($status == \Zidisha\Borrower\Borrower::PAYMENT_COMPLETE)
    <a href="{{ route('admin:repayment-process', \Zidisha\Borrower\Borrower::PAYMENT_INCOMPLETE) }}"> Show incomplete payments </a><br/>
    <a href="{{ route('admin:repayment-process', \Zidisha\Borrower\Borrower::PAYMENT_FAILED) }}"> Show failed payments </a><br/>
@elseif($status == \Zidisha\Borrower\Borrower::PAYMENT_INCOMPLETE)
    <a href="{{ route('admin:repayment-process', \Zidisha\Borrower\Borrower::PAYMENT_COMPLETE) }}"> Show ready to process payments </a><br/>
    <a href="{{ route('admin:repayment-process', \Zidisha\Borrower\Borrower::PAYMENT_FAILED) }}"> Show failed payments </a><br/>
@else
    <a href="{{ route('admin:repayment-process', \Zidisha\Borrower\Borrower::PAYMENT_COMPLETE) }}"> Show ready to process payments </a><br/>
    <a href="{{ route('admin:repayment-process', \Zidisha\Borrower\Borrower::PAYMENT_INCOMPLETE) }}"> Show incomplete payments </a><br/>
@endif
<a href="{{ route('admin:enter-repayment') }}"> Back To Enter Repayments </a><br/>
<p>  </p>

<br>
@if($deletable)
    {{ BootstrapForm::open(array('route' => 'admin:post-repayment-process', 'translationDomain' => 'repayment-delete',
    'id' => 'payment-delete')) }}
    <button id="payment-delete" class="btn btn-danger" type="submit"> Delete Selected </button>
    {{ BootstrapForm::hidden('status', $status) }}

@endif

<table class="table table-striped">
    <thead>
    <tr>
        <th> //TODO select all</th>
        <th>Date</th>
        <th>Country</th>
        <th>Receipt</th>
        <th>Borrower</th>
        <th>Amount</th>
        <th>Details</th>
    </tr>
    </thead>
    <tbody>
    @foreach($payments as $payment)
    <tr>
        @if($deletable)
            <td>{{ BootstrapForm::checkbox('paymentIds[]', $payment->getId()) }}</td>
        @endif
        <td>{{ $payment->getDate()->format('d-m-Y') }}</td>
        <td>{{ $payment->getCountry()->getName() }}</td>
        <td>{{ $payment->getReceipt() }}</td>
        <td>{{ $payment->getBorrower()->getName() }}</td>
        <td>{{ $payment->getAmount() }}</td>
        <td>{{ $payment->getDetails() }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
@if($deletable)
    {{ BootstrapForm::close() }}
@endif

@stop