@extends('layouts.master')

@section('page-title')
Transaction History
@stop

@section('content')
<div class="page-header">
    <h1>Transaction History</h1>
</div>

<p>Balance available for lending or withdrawal: {{ $currentBalance }} </p>

<table class="table table-striped">
    <thead>
        <tr>
            <th>
                Transaction Date
            </th>
            <th>
                Transaction Description
            </th>
            <th>
                Amount (USD)
            </th>
            <th>
                Balance (USD)
            </th>
        </tr>
    </thead>
    <tbody>
    @foreach($paginator as $transaction)
        <tr>
            <td>{{ $transaction->getTransactionDate()->format('d-m-Y') }}</td>
            <td><a href="{{ route('loan:index', $transaction->getLoan()->getId()) }}">{{ $transaction->getDescription() }}</a></td>
            <td>{{ $transaction->getAmount() }}</td>
            <td>{{ $currentBalancePage }}</td>
        </tr>
    <?php $currentBalancePage -= $transaction->getAmount(); ?>
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->links() }}

@stop
