@extends('layouts.side-menu')

@section('page-title')
Transaction History
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.lender-links')
@stop

@section('page-content')
<p>Current lending credit: <strong>{{ $currentBalance }}</strong></p>

<br/><br/>

<table class="table table-striped no-more-tables">
    <thead>
        <tr>
            <th>
                Date
            </th>
            <th>
                Description
            </th>
            <th>
                Amount
            </th>
            <th>
                Balance
            </th>
        </tr>
    </thead>
    <tbody>
    @foreach($paginator as $transaction)
        <tr>
            <td data-title="Date">{{ $transaction->getTransactionDate()->format('M j, Y') }}</td>
            <td data-title="Description">
                @if($transaction->getLoanId())
                    <a href="{{ route('loan:index', $transaction->getLoanId()) }}" target="_blank">{{ $transaction->getDescription() }}</a>
                @else
                    {{ $transaction->getDescription() }}
                @endif
            </td>
            <td data-title="Amount">{{ $transaction->getAmount()->getAmount() }}</td>
            <td data-title="Balance">{{ $currentBalancePage->getAmount() }}</td>
        </tr>
    <?php $currentBalancePage = $currentBalancePage->subtract($transaction->getAmount()); ?>
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->links() }}
@stop
