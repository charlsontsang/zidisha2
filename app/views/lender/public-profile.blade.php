@extends('layouts.master')

@section('page-title')
{{ $lender->getUser()->getUsername() }}
@stop

@section('content')
<div class="page-header">
    <h3><strong>{{ $lender->getUser()->getUsername() }}</strong></h3>
</div>

<img src="{{ $lender->getUser()->getProfilePictureUrl() }}">

<p><strong>About me: </strong> {{ $lender->getProfile()->getAboutMe() }} </p>
<p><strong>City: </strong> {{ $lender->getProfile()->getCity() }} </p>
<p><strong>Country: </strong> {{ $lender->getCountry()->getName() }} </p>
<p><strong>Karma: </strong><a href="#" class="karma" data-toggle="tooltip">(?)</a> {{ $karma }} </p>

<div class="page-header">
    <h3><strong>Active Bids</strong></h3>
</div>
<table class="table table-striped">
    <thead>
    <tr>
        <th>
            Date
        </th>
        <th>
            Borrower Details
        </th>
        <th>
            Amount Bid (USD)
        </th>
        <th>
            Bid Status
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($activeBids as $activeBid)
    <tr>
        <td>{{ $activeBid->getBidAt()->format('d-m-Y') }}</td>
        <td>
            <a href="{{ route('loan:index', $activeBid->getLoanId()) }}">{{ $activeBid->getBorrower()->getName() }}</a>
            {{ $activeBid->getBorrower()->getProfile()->getCity() }},
            {{ $activeBid->getBorrower()->getCountry()->getName() }}
        </td>
        <td>{{ $activeBid->getBidAmount()->getAmount() }}</td>
        <td> Active </td>
    </tr>
    @endforeach
    <tr>
        <td><strong>Total Current Value</strong></td>
        <td></td>
        <td>{{ $totalBidAmount->getAmount() }}</td>
        <td></td>
    </tr>
    </tbody>
</table>
{{ BootstrapHtml::paginator($activeBids)->links() }}

<div class="page-header">
    <h3><strong>Active Loans</strong></h3>
</div>
<table class="table table-striped">
    <thead>
    <tr>
        <th>
            Borrower Details
        </th>
        <th>
            Amount Lent (USD)
        </th>
        <th>
            Loan Status
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($activeLoansBids as $activeLoansBid)
    <tr>
        <td>
            <a href="{{ route('loan:index', $activeLoansBid->getLoanId()) }}">{{ $activeLoansBid->getBorrower()->getName() }}</a>
            {{ $activeLoansBid->getBorrower()->getProfile()->getCity() }},
            {{ $activeLoansBid->getBorrower()->getCountry()->getName() }}
        </td>
        <td>{{ $activeLoansBid->getAcceptedAmount()->getAmount() }}</td>
        <td>
            <a href="{{ route('loan:index', $activeLoansBid->getLoanId()) }}">
            {{ $activeLoansBid->getLoan()->getRepaidPercent() }} % Repaid
            </a>
        </td>
    </tr>
    @endforeach
    <tr>
        <td><strong>Total Amount Lent</strong></td>
        <td>{{ $totalActiveLoansBidsAmount->getAmount() }}</td>
        <td></td>
    </tr>
    </tbody>
</table>
{{ BootstrapHtml::paginator($activeLoansBids, 'page2')->links() }}


<div class="page-header">
    <h3><strong>Completed Loans</strong></h3>
</div>
<table class="table table-striped">
    <thead>
    <tr>
        <th>
            Borrower Details
        </th>
        <th>
            Amount Lent (USD)
        </th>
        <th>
            Loan Status
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($completedLoansBids as $completedLoansBid)
    <tr>
        <td>
            <a href="{{ route('loan:index', $completedLoansBid->getLoanId()) }}">{{ $completedLoansBid->getBorrower()->getName() }}</a>
            {{ $completedLoansBid->getBorrower()->getProfile()->getCity() }},
            {{ $completedLoansBid->getBorrower()->getCountry()->getName() }}
        </td>
        <td>{{ $completedLoansBid->getAcceptedAmount()->getAmount() }}</td>
        <td>
            <a href="{{ route('loan:index', $completedLoansBid->getLoanId()) }}">
            {{ $completedLoansBid->getLoan()->getRepaidPercent() }} % Repaid
            </a>
        </td>
    </tr>
    @endforeach
    <tr>
        <td><strong>Total Amount Lent</strong></td>
        <td>{{ $totalCompletedLoansBidsAmount->getAmount() }}</td>
        <td></td>
    </tr>
    </tbody>
</table>
{{ BootstrapHtml::paginator($completedLoansBids, 'page3')->links() }}
@stop

@section('script-footer')
<script type="text/javascript">
    $('.karma').tooltip({placement: 'bottom', title: 'Karma is calculated on the basis of the total amount lent by the new members a member has recruited to Zidisha via email invites or gift cards, the number of comments a member has posted in the Zidisha website, and the total amount lent by a member.'})
</script>
@stop