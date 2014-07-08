@extends('layouts.master')

@section('page-title')
GiftCards
@stop

@section('content')
<table class="table table-striped">
    <thead>
    <tr>
        <th>Order Type</th>
        <th>Card Amount</th>
        <th>Recipient Email</th>
        <th>Recipient Name</th>
        <th>Sender Name</th>
        <th>Sender Email</th>
        <th>Redemption Code</th>
        <th>Status</th>
        <th>Resend</th>
    </tr>
    </thead>
    <tbody>
    @foreach($paginator as $card)
    <tr>
        <td>{{ $card->getOrderType() }}</td>
        <td>{{ $card->getCardAmount() }}</td>
        <td>{{ $card->getRecipientEmail() }}</td>
        <td>{{ $card->getRecipientName() }}</td>
        <td>{{ $card->getLender()->getName() }}</td>
        <td>{{ $card->getLender()->getUser()->getEmail() }}</td>
        <td><a href="#TODO">{{ $card->getCardCode() }}</a></td>
        <td>
            {{ $card->getStringClaimed() }}
        </td>
        <td>
            @if($card->getOrderType() == "Email" && !$card->getClaimed())
            <a href="{{ route('admin:resend', $card->getId()) }}">Resend Email</a>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->links() }}
@stop