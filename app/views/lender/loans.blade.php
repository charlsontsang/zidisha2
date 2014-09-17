@extends('layouts.master')

@section('page-title')
My Stats
@stop

@section('content')
<div class="page-header">
    <h1>Your Loans</h1>
</div>

<div class="row text-large">
    <div class="col-sm-5">
        <div class="page-header">
            <h3><strong>Lending Totals</strong></h3>
        </div>

        <div class="row">
            <div class="col-sm-7">
                <p class="text-light">Funds uploaded:{{ BootstrapHtml::tooltip('lender.tooltips.loans.funds-uploaded') }}</p>
            </div>
            <div class="col-sm-5">
                <p>{{ $totalFundsUpload }}</p>
            </div>

            <div class="col-sm-7">
                <p class="text-light">Number of loans made:</p>
            </div>

            <div class="col-sm-5">
                <p>{{ $numberOfLoans }}</p>
            </div>

            <div class="col-sm-7">
                <p class="text-light">Total amount lent:</p>
            </div>

            <div class="col-sm-5">
                <p>{{ $totalLentAmount }}</p>
            </div>
        </div>
    </div>

    <div class="col-sm-5 col-sm-offset-1">
        <div class="page-header">
            <h3><strong>Current Status</strong></h3>
        </div>
        <div class="row">
            <div class="col-sm-7">
                <p class="text-light">Loans outstanding:{{ BootstrapHtml::tooltip('lender.tooltips.loans.loans-outstanding') }}</p>
            </div>
            <div class="col-sm-5">
                <p>{{ $principleOutstanding }}</p>
            </div>

            <div class="col-sm-7">
                <p class="text-light">Lending credit available:{{ BootstrapHtml::tooltip('lender.tooltips.loans.lending-credit-available') }}</p>
            </div>

            <div class="col-sm-5">
                <p>{{ $currentBalance }}</p>
            </div>

            @if ($lenderInviteCredit)
            <div class="col-sm-7">
                <p class="text-light">New member invite credit:</p>
            </div>

            <div class="col-sm-5">
                <p>{{ $lenderInviteCredit }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

@if ($fundraisingLoanBids->count())

<div class="page-header">
    <h3><strong>Fundraising Loans</strong></h3>
</div>
<table class="table table-striped no-more-tables">
    <thead>
    <tr>
        <th colspan="2" width="25%">Entrepreneur</th>
        <th width="15%">Project</th>
        <th width="15%">Date Funded</th>
        <th width="15%">Amount Lent</th>
        <th width="30%">Fundraising Progress</th>
    </tr>
    </thead>
    <tbody>
    @foreach($fundraisingLoanBids as $fundraisingLoanBid)
    <tr>
        <td data-title="Entrepreneur">
            <a class="pull-left" href="{{ route('loan:index', $fundraisingLoanBid->getLoanId()) }}">
                <img src="{{ $fundraisingLoanBid->getBorrower()->getUser()->getProfilePictureUrl('small-profile-picture') }}" width="100%">
            </a>
        </td>
        <td>
            {{ $fundraisingLoanBid->getBorrower()->getName() }}
            <br/><br/>
            {{ $fundraisingLoanBid->getBorrower()->getCountry()->getName() }}
        </td>
        <td data-title="Project">
            <a href="{{ route('loan:index', $fundraisingLoanBid->getLoanId()) }}">{{ $fundraisingLoanBid->getLoan()->getSummary() }}</a>
        </td>
        <td data-title="Date Funded">{{ $fundraisingLoanBid->getBidAt()->format('M j, Y') }}</td>
        <td data-title="Amount Lent">{{ $fundraisingLoanBid->getBidAmount()->getAmount() }}</td>
        <td data-title="Progress"> @include('partials/loan-progress', [ 'loan' => $fundraisingLoanBid->getLoan() ]) </td>
    </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td>{{ \Lang::choice(
                       'lender.shared-labels.projects.stats-projects',
                       $fundraisingLoanBids->getTotal(),
                       ['count' => $fundraisingLoanBids->getTotal()]
                ) }}
            </td>
            <td>{{ $fundraisingLoanBids->getTotalBidAmount()->round(2)->getAmount() }} Lent</td>
            <td></td>
        </tr>
    </tfoot>
</table>
{{ $fundraisingLoanBids->getPaginator()->links() }}
@endif

@if ($activeLoanBids->count())
<div class="page-header">
    <h3><strong>Active Loans</strong></h3>
</div>

<table class="table table-striped no-more-tables">
    <thead>
    <tr>
        <th colspan="2" width="25%">Entrepreneur</th>
        <th width="15%">Project</th>
        <th width="15%">Date Funded</th>
        <th width="15%">Amount Lent</th>
        <th width="15%">
            Amount Repaid
            {{ BootstrapHtml::tooltip('lender.tooltips.loans.amount-repaid-active-loans') }}
        </th>
        <th width="15%">
            Amount Outstanding
            {{ BootstrapHtml::tooltip('lender.tooltips.loans.amount-outstanding-active-loans') }}
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($activeLoanBids as $activeLoanBid)
    <tr>
        <td data-title="Entrepreneur">
            <a class="pull-left" href="{{ route('loan:index', $activeLoanBid->getLoanId()) }}">
                <img src="{{ $activeLoanBid->getBorrower()->getUser()->getProfilePictureUrl('small-profile-picture') }}" width="100%">
            </a>
        </td>
        <td>
            {{ $activeLoanBid->getBorrower()->getName() }}
            <br/><br/>
            {{ $activeLoanBid->getBorrower()->getCountry()->getName() }}
        </td>
        <td data-title="Project">
            <a href="{{ route('loan:index', $activeLoanBid->getLoanId()) }}">{{ $activeLoanBid->getLoan()->getSummary() }}</a>
        </td>
        <td data-title="Date Funded">
            {{ $activeLoanBid->getFundedAt()->format('M j, Y') }}
        </td>
        <td data-title="Amount Lent">{{ $activeLoanBid->getLentAmount()->getAmount() }}</td>
        <td data-title="Amount Repaid">{{ $activeLoanBid->getRepaidAmount()->getAmount() }}</td>
        <td data-title="Outstanding">
            {{ $activeLoanBid->getOutstandingAmount()->getAmount() }}
            <br/><br/>
            @if($activeLoanBid->getLoanPaymentStatus() == 'on-time')
                <span class="label label-success">Repaying on Time</span>
            @elseif($activeLoanBid->getLoanPaymentStatus() == 'late')
                <span class="label label-default">Repaying Late</span>
            @elseif($activeLoanBid->getLoanPaymentStatus() == 'early')
                <span class="label label-success">Repaying Early</span>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td>{{ \Lang::choice(
                       'lender.shared-labels.projects.stats-projects',
                       $activeLoanBids->getTotal(),
                       ['count' => $activeLoanBids->getTotal()]
                ) }}
            </td>
            <td>{{ $activeLoanBids->getTotalLentAmount() }} Lent</td>
            <td>{{ $activeLoanBids->getTotalRepaidAmount() }} Repaid</td>
            <td>{{ $activeLoanBids->getTotalOutstandingAmount() }} Outstanding</td>
        </tr>
    </tfoot>
</table>
{{ $activeLoanBids->getPaginator('page2')->links() }}
@endif

@if (count($completedLoansBids)>0)
<div class="page-header">
    <h3><strong>Completed Loans</strong></h3>
</div>
<table class="table table-striped no-more-tables">
    <thead>
    <tr>
        <th colspan="2" width="25%">Entrepreneur</th>
        <th width="15%">Project</th>
        <th width="15%">Date Funded</th>
        <th width="15%">Amount Lent</th>
        <th width="15%">
            Amount Repaid
            {{ BootstrapHtml::tooltip('lender.tooltips.loans.amount-repaid-completed-loans') }}
        </th>
        <th width="15%">
            Net Change in Loan Fund Value
            {{ BootstrapHtml::tooltip('lender.tooltips.loans.net-change-completed-loans') }}
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($completedLoansBids as $completedLoansBid)
    <tr>
        <td data-title="Entrepreneur">
            <a class="pull-left" href="{{ route('loan:index', $completedLoansBid->getLoanId()) }}">
                <img src="{{ $completedLoansBid->getBorrower()->getUser()->getProfilePictureUrl('small-profile-picture') }}" width="100%">
            </a>
        </td>
        <td>
            {{ $completedLoansBid->getBorrower()->getName() }}
            <br/><br/>
            {{ $completedLoansBid->getBorrower()->getCountry()->getName() }}
        </td>
        <td data-title="Project"><a href="{{ route('loan:index', $completedLoansBid->getLoanId()) }}">{{ $completedLoansBid->getLoan()->getSummary() }}</a></td>
        <td data-title="Date Funded">{{ $completedLoansBid->getLoan()->getDisbursedAt()->format('M j, Y') }}</td>
        <td data-title="Amount Lent">{{ $completedLoansBid->getAcceptedAmount()->getAmount() }}</td>
        <td data-title="Amount Repaid">
            {{ $completedLoansBidAmountRepaid[$completedLoansBid->getId()]->getAmount() }}
            <br/><br/>
            <a href="{{ route('loan:index', $completedLoansBid->getLoanId()) }}#feedback">Leave Feedback</a>
        </td>
        <td data-title="Net Change">{{ $netChangeCompletedBid[$completedLoansBid->getId()]->getAmount() }} </td>
    </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td>{{ $numberOfCompletedProjects }}</td>
            <td>{{ $totalCompletedLoansBidsAmount->getAmount() }} Lent</td>
            <td>{{ $totalCompletedLoansRepaidAmount->getAmount() }} Repaid</td>
            <td>{{ $totalNetChangeCompletedBid->getAmount() }} Net Change in Loan Fund Value</td>
        </tr>
    </tfoot>
</table>
{{ BootstrapHtml::paginator($completedLoansBids, 'page3')->links() }}
@endif
@stop

@section('script-footer')
<script type="text/javascript">
    $('.total-impact').tooltip({placement: 'bottom', title: 'The total amount lent by you, your invitees and your gift card recipients.'})
</script>
<script type="text/javascript">
    $('.loan-status-active-loans').tooltip({placement: 'bottom', title: 'Loans are labeled "Repaying Early" or "Repaying Late" if repayments are more than 10 days ahead or behind schedule, using a threshold of $10 or the value of one installment (whichever is greater). Otherwise, they are labeled "On Time.'})
</script>
@stop
