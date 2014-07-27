@extends('layouts.master')

@section('page-title')
Track My Gift Cards
@stop

@section('content')
<h1>Track My Gift Cards</h1>

<div class="raw">
    <div class="col-xs-8">
        <p> Gift Cards Gifted: {{ $countCards }} </p>

        <p> Gift Cards Redeemed by My Recipients: {{ $countRedeemed }} </p>
    </div>
    <div class="col-xs-4">
        <a href="{{ route('lender:gift-cards') }}">Purchase Gift Card</a>
    </div>
</div>

<br/>

<table class="table table-striped">
    <thead>
    <tr>
        <th>Date Gifted</th>
        <th>Name</th>
        <th>Delivery Method</th>
        <th>Recipient Email
        <th>Amount
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($cards as $card)
    <tr>
        <td>{{ $card->getDate()->format('d-m-Y') }}</td>
        <td>{{ $card->getRecipientName() }}</td>
        <td>{{ $card->getOrderType() }}</td>
        <td>{{ $card->getRecipientEmail() }}</td>
        <td>{{ $card->getCardAmount()->getAmount() }}</td>
        @if($card->getClaimed() == 1)
        <td>
            <span class="label label-success">{{ $card->getStringClaimed() }}</span>
        </td>
        @else
        <td>
            <span class="label label-info">{{ $card->getStringClaimed() }}</span>
        </td>
        @endif
    </tr>
    @endforeach
    </tbody>
</table>

@stop
