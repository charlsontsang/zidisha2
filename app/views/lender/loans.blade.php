@extends('layouts.master')

@section('page-title')
My Stats
@stop

@section('content')
<div class="page-header">
    <h2>Your Loans</h2>
</div>

<div class="row">
    <div class="col-sm-5">
        <div class="page-header">
            <h3><strong>Lending Totals</strong></h3>
        </div>

        <div class="row">
            <div class="col-sm-7">
                <p>Funds uploaded:<i class="fa fa-info-circle funds-upload" data-toggle="tooltip"></i></p>
            </div>
            <div class="col-sm-5">
                <p>{{ $totalFundsUpload }}</p>
            </div>

            <div class="col-sm-7">
                Number of loans made:
            </div>

            <div class="col-sm-5">
                <p>{{ $numberOfLoans }}</p>
            </div>

            <div class="col-sm-7">
                <p>Total amount lent:</p>
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
                <p>Loans outstanding:<i class="fa fa-info-circle principal-outstanding" data-toggle="tooltip"></i></p>
            </div>
            <div class="col-sm-5">
                <p>{{ $principleOutstanding }}</p>
            </div>

            <div class="col-sm-7">
                <p>Lending credit available:<i class="fa fa-info-circle credit-available" data-toggle="tooltip"></i></p>
            </div>

            <div class="col-sm-5">
                <p>{{ $currentBalance }}</p>
            </div>

            @if ($lenderInviteCredit)
            <div class="col-sm-7">
                <p>New member invite credit:</p>
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
        <th colspan="3" width="50%">Project</th>
        <th>Date Funded</th>
        <th>Amount Lent</th>
        <th>Fundraising Progress</th>
    </tr>
    </thead>
    <tbody>
    @foreach($fundraisingLoanBids as $fundraisingLoanBid)
    <tr>
        <td data-title="Project">
            <a class="pull-left" href="{{ route('loan:index', $fundraisingLoanBid->getLoanId()) }}">
                <img src="{{ $fundraisingLoanBid->getBorrower()->getUser()->getProfilePictureUrl('small-profile-picture') }}" width="100%">
            </a>
        </td>
        <td>
            {{ $fundraisingLoanBid->getBorrower()->getName() }}
            <br/><br/>
            {{ $fundraisingLoanBid->getBorrower()->getProfile()->getCity() }},
            {{ $fundraisingLoanBid->getBorrower()->getCountry()->getName() }}
        </td>
        <td>
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
                       'lender.flash.preferences.stats-projects',
                       $fundraisingLoanBids->getTotal(),
                       ['count' => $fundraisingLoanBids->getTotal()]
                ) }}
            </td>
            <td>{{ $fundraisingLoanBids->getTotalBidAmount()->round(2)->getAmount() }} Lent</td>
        </tr>
    </tfoot>
</table>
{{ $fundraisingLoanBids->getPaginator()->links() }}
@endif

@if (count($activeLoanBids)>0)
<div class="page-header">
    <h3><strong>Active Loans</strong></h3>
</div>

<table class="table table-striped no-more-tables">
    <thead>
    <tr>
        <th colspan="3" width="50%">Project</th>
        <th>Date Funded</th>
        <th>Amount Lent</th>
        <th>Amount Repaid<i class="fa fa-info-circle amount-repaid-active-loans" data-toggle="tooltip"></i></th>
        <th>Amount Outstanding<i class="fa fa-info-circle principal-outstanding-active-loans" data-toggle="tooltip"></i></th>
    </tr>
    </thead>
    <tbody>
    @foreach($activeLoanBids as $activeLoanBid)
    <tr>
        <td data-title="Project">
            <a class="pull-left" href="{{ route('loan:index', $activeLoanBid->getLoanId()) }}">
                <img src="{{ $activeLoanBid->getBorrower()->getUser()->getProfilePictureUrl('small-profile-picture') }}" width="100%">
            </a>
        </td>
        <td>
            {{ $activeLoanBid->getBorrower()->getName() }}
            <br/><br/>
            {{ $activeLoanBid->getBorrower()->getProfile()->getCity() }},
            {{ $activeLoanBid->getBorrower()->getCountry()->getName() }}
        </td>
        <td><a href="{{ route('loan:index', $activeLoanBid->getLoanId()) }}">{{ $activeLoanBid->getLoan()->getSummary() }}</a></td>
        <td data-title="Date Funded">
        @if($activeLoanBid->getLoan()->getStatus() == Zidisha\Loan\Loan::ACTIVE)
            {{ $activeLoanBid->getLoan()->getDisbursedAt()->format('M j, Y') }}
        @else
            {{ $activeLoanBid->getLoan()->getAcceptedAt()->format('M j, Y') }}
        @endif
        </td>
        <td data-title="Amount Lent">{{ $activeLoanBid->getAcceptedAmount()->getAmount() }}</td>
        <td data-title="Amount Repaid">{{ $activeLoanBidAmountRepaid[$activeLoanBid->getId()]->getAmount() }}</td>
        <td data-title="Outstanding">
            {{ $activeLoanBidPrincipleOutstanding[$activeLoanBid->getId()]->getAmount() }}
            <br/><br/>
            @if($activeLoanBidPaymentStatus[$activeLoanBid->getId()] == 'on-time')
                    <span class="label label-success">Repaying on Time</span>
            @elseif($activeLoanBidPaymentStatus[$activeLoanBid->getId()] == 'late')
                    <span class="label label-default">Repaying Late</span>
            @elseif($activeLoanBidPaymentStatus[$activeLoanBid->getId()] == 'early')
                    <span class="label label-success">Repaying Early</span>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td>{{ $numberOfActiveProjects }}</td>
            <td>{{ $totalActiveLoanBidsAmount->getAmount() }} Lent</td>
            <td>{{ $totalActiveLoansRepaidAmount->getAmount() }} Repaid</td>
            <td>{{ $totalActiveLoansTotalOutstandingAmount->getAmount() }} Outstanding</td>
        </tr>
    </tfoot>
</table>
{{ BootstrapHtml::paginator($activeLoanBids, 'page2')->links() }}
@endif

@if (count($completedLoansBids)>0)
<div class="page-header">
    <h3><strong>Completed Loans</strong></h3>
</div>
<table class="table table-striped no-more-tables">
    <thead>
    <tr>
        <th colspan="3" width="50%">Project</th>
        <th>Date Funded</th>
        <th>Amount Lent</th>
        <th>Amount Repaid<i class="fa fa-info-circle amount-repaid-completed-loans" data-toggle="tooltip"></i></th>
        <th>Net Change in Loan Fund Value<i class="fa fa-info-circle net-change-completed-loans" data-toggle="tooltip"></i></th>
    </tr>
    </thead>
    <tbody>
    @foreach($completedLoansBids as $completedLoansBid)
    <tr>
        <td data-title="Project">
            <a class="pull-left" href="{{ route('loan:index', $completedLoansBid->getLoanId()) }}">
                <img src="{{ $completedLoansBid->getBorrower()->getUser()->getProfilePictureUrl('small-profile-picture') }}" width="100%">
            </a>
        </td>
        <td>
            {{ $completedLoansBid->getBorrower()->getName() }}
            <br/><br/>
            {{ $completedLoansBid->getBorrower()->getProfile()->getCity() }},
            {{ $completedLoansBid->getBorrower()->getCountry()->getName() }}
        </td>
        <td><a href="{{ route('loan:index', $completedLoansBid->getLoanId()) }}">{{ $completedLoansBid->getLoan()->getSummary() }}</a></td>
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
    $('.funds-upload').tooltip({placement: 'bottom', title: 'The total amount of funds you have uploaded into your account as lending credit. Does not include loan repayments credited to your account.'})
</script>
<script type="text/javascript">
    $('.credit-available').tooltip({placement: 'bottom', title: 'The current balance of credit available for lending, composed of lender fund uploads and repayments received, which have not been withdrawn or bid on new loans. Does not include amounts in your Lending Cart.'})
</script>
<script type="text/javascript">
    $('.principal-outstanding').tooltip({placement: 'bottom', title: 'The portion of US dollar amounts you have lent which is still outstanding with the borrowers (not yet repaid). This amount does not include any interest which is due for the loans, and its value is not adjusted for credit risk or exchange rate fluctuations.'})
</script>
<script type="text/javascript">
    $('.total-impact').tooltip({placement: 'bottom', title: 'The total amount lent by you, your invitees and your gift card recipients.'})
</script>
<script type="text/javascript">
    $('.amount-repaid-active-loans').tooltip({placement: 'bottom', title: 'This is the amount that has been repaid into your lending account for this loan, including interest and adjusted for currency exchange rate fluctuations.'})
</script>
<script type="text/javascript">
    $('.principal-outstanding-active-loans').tooltip({placement: 'bottom', title: 'The portion of US dollar amounts you have lent which is still outstanding with the borrowers (not yet repaid). This amount does not include any interest which is due for the loans, and its value is not adjusted for credit risk or exchange rate fluctuations.'})
</script>
<script type="text/javascript">
    $('.loan-status-active-loans').tooltip({placement: 'bottom', title: 'Loans are labeled "Repaying Early" or "Repaying Late" if repayments are more than 10 days ahead or behind schedule, using a threshold of $10 or the value of one installment (whichever is greater). Otherwise, they are labeled "On Time.'})
</script>
<script type="text/javascript">
    $('.amount-repaid-completed-loans').tooltip({placement: 'bottom', title: 'This is the amount that has been repaid into your lending account for this loan, including interest and adjusted for currency exchange rate fluctuations.'})
</script>
<script type="text/javascript">
    $('.net-change-completed-loans').tooltip({placement: 'bottom', title: 'This is the amount by which this loan increased or decreased the total value of your loan fund. It is the difference between the amount you originally paid to fund this loan, and the total amount that was returned to your account after currency fluctuations, interest and any writeoff or forgiveness of outstanding principal.'})
</script>
@stop
