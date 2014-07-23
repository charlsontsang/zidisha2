@extends('layouts.master')

@section('page-title')
Join the global P2P microlending movement
@stop

@section('content')
<div class="page-header">
    <h3><strong>Lender Details</strong></h3>
</div>

<img src="{{ $lender->getUser()->getProfilePictureUrl() }}">

<p><strong>Username: </strong> {{ $lender->getUser()->getUsername() }} </p> <br>

<p><strong>About me: </strong> {{ $lender->getProfile()->getAboutMe() }} </p> <br>

<p><strong>City: </strong> {{ $lender->getProfile()->getCity() }} </p> <br>
<p><strong>Country: </strong> {{ $lender->getCountry()->getName() }} </p> <br>
<p><strong>Karma: </strong><a href="#" class="karma" data-toggle="tooltip">(?)</a> {{ $karma }} </p> <br>

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

@stop

@section('script-footer')
<script type="text/javascript">
    $('.karma').tooltip({placement: 'bottom', title: 'Karma is calculated on the basis of the total amount lent by the new members a member has recruited to Zidisha via email invites or gift cards, the number of comments a member has posted in the Zidisha website, and the total amount lent by a member.'})
</script>
@stop