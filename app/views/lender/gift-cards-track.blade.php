@extends('layouts.master')

@section('page-title')
Track Gift Cards
@stop

@section('content')
<div class="page-header">
    <h2>Track Gift Cards</h2>
</div>

<div class="row">
    <div class="col-sm-5">

        <div class="row">
            <div class="col-sm-7">
                <p>Gift Cards Gifted:</p>
            </div>
            <div class="col-sm-5">
                <p>{{ $countCards }}</p>
            </div>

            <div class="col-sm-7">
                Gift Cards Redeemed:
            </div>

            <div class="col-sm-5">
                <p>{{ $countRedeemed }}</p>
            </div>
        </div>
    </div>

    <div class="col-sm-5 col-sm-offset-1">
        <div class="row">
            <div class="col-sm-7">
                <a href="{{ route('lender:gift-cards') }}" class="btn btn-primary">
                    @if ($countCards==0)
                        Give your first gift card
                    @else
                        Give another gift card
                    @endif
                </a>
            </div>
        </div>
    </div>
</div>

<br/><br/>

<table class="table table-striped no-more-tables">
    <thead>
    <tr>
        <th>Date Gifted</th>
        <th>Recipient Name</th>
        <th>Delivery Method</th>
        <th>Recipient Email
        <th>Card Amount</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($cards as $card)
    <tr>
        <td data-title="Date Gifted">{{ $card->getDate()->format('M j, Y') }}</td>
        <td data-title="Recipient Name">{{ $card->getRecipientName() }}</td>
        <td data-title="Delivery Method">{{ $card->getOrderType() }}</td>
        <td data-title="Recipient Email">{{ $card->getRecipientEmail() }}</td>
        <td data-title="Card Amount">{{ $card->getCardAmount()->getAmount() }}</td>
        <td data-title="Status">
            @if($card->getClaimed() == 1)
            <span class="label label-success">{{ $card->getStringClaimed() }}</span>
        @else
            <span class="label label-info">{{ $card->getStringClaimed() }}</span>
        </td>
        @endif
    </tr>
    @endforeach
    </tbody>
</table>

@stop
