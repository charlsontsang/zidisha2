@extends('layouts.master')

@section('page-title')
Transaction History
@stop

@section('content')
<div class="row">
    <div class="col-sm-3 col-md-4">
        <ul class="nav side-menu" role="complementary">
          <h4>Quick Links</h4>
            @include('partials.nav-links.lender-links')       
          </ul>
    </div>

    <div class="col-sm-9 col-md-8 info-page">
        <div class="page-header">
            <h1>Transaction History</h1>
        </div>

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
    </div>
</div>
@stop
