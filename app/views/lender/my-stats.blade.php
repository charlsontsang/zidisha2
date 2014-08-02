@extends('layouts.master')

@section('page-title')
My Stats
@stop

@section('content')
<div class="page-header">
    <h2>My Loans</h2>
</div><br>

<div class="row">
    <div class="col-xs-4">
        <p>Total Funds Uploaded: <a href="#" class="funds-upload" data-toggle="tooltip">(?)</a>  </p>
        <p>Total Amount Lent:</p>
        <p>Number of Loans Made:  </p>
        <p>Current Credit Available: <a href="#" class="credit-available" data-toggle="tooltip">(?)</a>  </p>
        <p>New Member Invite Credit: </p>
        <p>Principal Outstanding: <a href="#" class="principal-outstanding" data-toggle="tooltip">(?)</a>  </p>
    </div>

    <div class="col-xs-8">
        <p>{{ $totalFundsUpload }}</p>
        <p>{{ $totalLentAmount }}</p>
        <p>{{ $numberOfLoans }}</p>
        <p>{{ $currentBalance }}</p>
        <p>{{ $newMemberInviteCredit }}</p>
        <p>{{ $principleOutstanding }}</p>
    </div>
</div>

<div class="page-header">
    <h3><strong>Fundraising Loans</strong></h3>
</div>
<table class="table table-striped">
    <thead>
    <tr>
        <th>Project</th>
        <th>Bid Date</th>
        <th>Amount Bid (USD)</th>
        <th>FundRaising Progress</th>
        <th>Time Left</th>
    </tr>
    </thead>
    <tbody>
    @foreach($activeBids as $fundRaisingLoansBid)
    <tr>
        <td>
<!--            <a class="pull-left" href="{{ route('loan:index', $fundRaisingLoansBid->getLoanId()) }}">-->
<!--                <img src="{{ $fundRaisingLoansBid->getBorrower()->getUser()->getProfilePictureUrl('small-profile-picture') }}" width="100%">-->
<!--            </a>-->
            <a href="{{ route('loan:index', $fundRaisingLoansBid->getLoanId()) }}">{{ $fundRaisingLoansBid->getLoan()->getSummary() }}</a><br>
            {{ $fundRaisingLoansBid->getBorrower()->getName() }}<br>
            {{ $fundRaisingLoansBid->getBorrower()->getProfile()->getCity() }},
            {{ $fundRaisingLoansBid->getBorrower()->getCountry()->getName() }}
        </td>
        <td>{{ $fundRaisingLoansBid->getBidAt()->format('d-m-Y') }}</td>
        <td>{{ $fundRaisingLoansBid->getBidAmount()->getAmount() }}</td>
        <td> @include('partials/loan-progress', [ 'loan' => $fundRaisingLoansBid->getLoan() ]) </td>
        <td>{{ $fundRaisingLoansBid->getLoan()->getFundRaisingTimeLeft() }}</td>
    </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td><strong> Total </strong></td>
            <td>{{ $numberOfFundRaisingProjects }}</td>
            <td>{{ $totalBidAmount->getAmount() }}</td>
        </tr>
    </tfoot>
</table>
{{ BootstrapHtml::paginator($activeBids)->links() }}


<div class="page-header">
    <h3><strong>Active Loans</strong></h3>
</div>
<table class="table table-striped">
    <thead>
    <tr>
        <th>Project</th>
        <th>Date Funded</th>
        <th>Amount Lent (USD)</th>
        <th>Amount Repaid (USD) <a href="#" class="amount-repaid-active-loans" data-toggle="tooltip">(?)</a></th>
        <th>Principal Outstanding (USD) <a href="#" class="principal-outstanding-active-loans" data-toggle="tooltip">(?)</a></th>
        <th>Loan Status <a href="#" class="loan-status-active-loans" data-toggle="tooltip">(?)</a></th>
    </tr>
    </thead>
    <tbody>
    @foreach($activeLoansBids as $activeLoansBid)
    <tr>
        <td>
            <!--            <a class="pull-left" href="{{ route('loan:index', $fundRaisingLoansBid->getLoanId()) }}">-->
            <!--                <img src="{{ $fundRaisingLoansBid->getBorrower()->getUser()->getProfilePictureUrl('small-profile-picture') }}" width="100%">-->
            <!--            </a>-->
            <a href="{{ route('loan:index', $activeLoansBid->getLoanId()) }}">{{ $activeLoansBid->getLoan()->getSummary() }}</a><br>
            {{ $activeLoansBid->getBorrower()->getName() }}<br>
            {{ $activeLoansBid->getBorrower()->getProfile()->getCity() }},
            {{ $activeLoansBid->getBorrower()->getCountry()->getName() }}
        </td>
        <td>
        @if($activeLoansBid->getLoan()->getStatus() == Zidisha\Loan\Loan::ACTIVE)
            {{ $activeLoansBid->getLoan()->getDisbursedAt()->format('d-m-Y') }}
        @else
            {{ $activeLoansBid->getLoan()->getAcceptedAt()->format('d-m-Y') }}
        @endif
        </td>
        <td>{{ $activeLoansBid->getAcceptedAmount()->getAmount() }}</td>
        <td>{{ $activeLoansBidAmountRepaid[$activeLoansBid->getId()]->getAmount() }}</td>
        <td>{{ $activeLoansBidPrincipleOutstanding[$activeLoansBid->getId()]->getAmount() }}</td>
        <td>
            @if($activeLoansBidPaymentStatus[$activeLoansBid->getId()] == 'on-time')
                    <span class="label label-success">Repaying on Time</span>
            @elseif($activeLoansBidPaymentStatus[$activeLoansBid->getId()] == 'late')
                    <span class="label label-danger">Repaying Late</span>
            @elseif($activeLoansBidPaymentStatus[$activeLoansBid->getId()] == 'early')
                    <span class="label label-warning">Repaying Early</span>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td><strong> Total </strong></td>
            <td>{{ $numberOfActiveProjects }}</td>
            <td>{{ $totalActiveLoansBidsAmount->getAmount() }}</td>
            <td>{{ $totalActiveLoansRepaidAmount->getAmount() }}</td>
            <td>{{ $totalActiveLoansTotalOutstandingAmount->getAmount() }}</td>
        </tr>
    </tfoot>
</table>
{{ BootstrapHtml::paginator($activeLoansBids, 'page2')->links() }}


<div class="page-header">
    <h3><strong>Completed Loans</strong></h3>
</div>
<table class="table table-striped">
    <thead>
    <tr>
        <th>Project</th>
        <th>Date Funded</th>
        <th>Amount Lent (USD)</th>
        <th>Amount Repaid (USD)<a href="#" class="amount-repaid-completed-loans" data-toggle="tooltip">(?)</a></th>
        <th>Net Change in Loan Fund Value (USD)<a href="#" class="net-change-completed-loans" data-toggle="tooltip">(?)</a></th>
        <th>Loan Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($completedLoansBids as $completedLoansBid)
    <tr>
        <td>
            <!--            <a class="pull-left" href="{{ route('loan:index', $completedLoansBid->getLoanId()) }}">-->
            <!--                <img src="{{ $completedLoansBid->getBorrower()->getUser()->getProfilePictureUrl('small-profile-picture') }}" width="100%">-->
            <!--            </a>-->
            <a href="{{ route('loan:index', $completedLoansBid->getLoanId()) }}">{{ $completedLoansBid->getLoan()->getSummary() }}</a><br>
            {{ $completedLoansBid->getBorrower()->getName() }}<br>
            {{ $completedLoansBid->getBorrower()->getProfile()->getCity() }},
            {{ $completedLoansBid->getBorrower()->getCountry()->getName() }}
        </td>
        <td>{{ $completedLoansBid->getLoan()->getDisbursedAt()->format('d-m-Y') }}</td>
        <td>{{ $completedLoansBid->getAcceptedAmount()->getAmount() }}</td>
        <td>{{ $completedLoansBidAmountRepaid[$completedLoansBid->getId()]->getAmount() }}</td>
        <td>{{ $netChangeCompletedBid[$completedLoansBid->getId()]->getAmount() }} </td>
        <td>
            100% Repaid<br>
            <a href="{{ route('loan:index', $completedLoansBid->getLoanId()) }}#feedback">Leave Feedback</a>
        </td>
    </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td><strong> Total </strong></td>
            <td>{{ $numberOfCompletedProjects }}</td>
            <td>{{ $totalCompletedLoansBidsAmount->getAmount() }}</td>
            <td>{{ $totalCompletedLoansRepaidAmount->getAmount() }}</td>
            <td>{{ $totalNetChangeCompletedBid->getAmount() }}</td>
        </tr>
    </tfoot>
</table>
{{ BootstrapHtml::paginator($completedLoansBids, 'page3')->links() }}
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
