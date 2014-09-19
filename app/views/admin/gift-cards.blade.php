@extends('layouts.master')

@section('page-title')
Manage Gift Cards
@stop

@section('content')
<div class="page-header">
    <h1>Manage Gift Cards</h1>
</div>
<table class="table table-striped" id="cards">
    <thead>
    <tr>
        <th>Order Type</th>
        <th>Amount</th>
        <th>Recipient Name</th>
        <th>Recipient Email</th>
        <th>Sender Name</th>
        <th>Sender Email</th>
        <th>Redemption Code</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($paginator as $card)
    <tr>
        <td>{{ $card->getOrderType() }}</td>
        <td>{{ $card->getCardAmount() }}</td>
        <td>{{ $card->getRecipientName() }}</td>
        <td>{{ $card->getRecipientEmail() }}</td>
        <td>{{ $card->getLender()->getName() }}</td>
        <td>{{ $card->getLender()->getUser()->getEmail() }}</td>
        <td><a href="#TODO">{{ $card->getCardCode() }}</a></td>
        <td>
            <p>
                {{ $card->getStringClaimed() }}
            </p>
            @if($card->getOrderType() == "Email" && !$card->getClaimed())
                <p>
                    <a href="{{ route('admin:resend', $card->getId()) }}">Resend card email</a>
                </p>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->links() }}
@stop

@section('script-footer')
<script type="text/javascript">
    $(document).ready(function () {
        $('#cards').dataTable({
            'searching': true
        });
    });
</script>
@stop