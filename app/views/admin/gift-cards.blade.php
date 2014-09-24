@extends('layouts.master')

@section('page-title')
Manage Gift Cards
@stop

@section('content')
<h1 class="page-title">
    Manage Gift Cards
</h1>
<div class="panel panel-info">
    <div class="panel-body">
        <table class="table table-striped" id="cards">
            <thead>
            <tr>
                <th>Order Type</th>
                <th>Amount</th>
                <th>Recipient</th>
                <th>Sender</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach($paginator as $card)
            <tr>
                <td>{{ $card->getOrderType() }}</td>
                <td>{{ $card->getCardAmount() }}</td>
                <td>
                    <p>{{ $card->getRecipientName() }}</p>
                    <p>{{ $card->getRecipientEmail() }}</p>
                </td>
                <td>
                    <p>{{ $card->getLender()->getName() }}</p>
                    <p>{{ $card->getLender()->getUser()->getEmail() }}</p>
                </td>
                <td>
                    <p>
                        {{ $card->getStringClaimed() }}
                    </p>
                    <p>
                        Code: {{ $card->getCardCode() }}
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
    </div>
</div>
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