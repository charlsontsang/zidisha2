@extends('layouts.master')

@section('page-title')
Transaction History
@stop

@section('content')
<div class="page-header">
    <h1>Transaction History</h1>
</div>

<p>Balance available: ${{ $currentBalance }} </p>

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
                Amount (US $)
            </th>
            <th>
                Balance (US $)
            </th>
        </tr>
    </thead>
    <tbody>
    @foreach($paginator as $transaction)
        <tr>
            <td>{{ $transaction->getTransactionDate()->format('d-m-Y') }}</td>
            <td><a href="#">{{ $transaction->getDescription() }}</a></td>
            <td>{{ $transaction->getAmount()->getAmount() }}</td>
            <td>{{ $currentBalancePage->getAmount() }}</td>
        </tr>
    <?php $currentBalancePage = $currentBalancePage->subtract($transaction->getAmount()); ?>
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->links() }}

@stop
