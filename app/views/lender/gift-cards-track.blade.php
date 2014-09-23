@extends('layouts.master')

@section('page-title')
Track Gift Cards
@stop

@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2 text-center">
        <div class="page-header">
            <h1>Track Gift Cards</h1>
        </div>
    </div>
</div>

<div class="panel panel-info">
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-3">
                Gift Cards Gifted: <strong>{{ $countCards }}</strong> 
            </div>
            <div class="col-sm-3">
                Gift Cards Redeemed: <strong>{{ $countRedeemed }}</strong>
            </div>
            {{--<div class="col-sm-6">--}}
                {{--<a href="{{ route('lender:gift-cards') }}" class="btn btn-primary pull-right">--}}
                    {{--@if ($countCards==0)--}}
                        {{--Give your first gift card--}}
                    {{--@else--}}
                        {{--Give another gift card--}}
                    {{--@endif--}}
                {{--</a>--}}
            {{--</div>--}}
        </div>
        <table class="table table-striped no-more-tables" id="track-cards">
            <thead>
                <tr>
                    <th>Date Gifted</th>
                    <th>Recipient Name</th>
                    <th>Delivery Method</th>
                    <th>Card Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cards as $card)
                <tr>
                    <td data-title="Date">{{ $card->getDate()->format('M j, Y') }}</td>
                    <td data-title="Recipient">{{ $card->getRecipientName() }}</td>
                    <td data-title="Delivery">
                        @if (!empty($card->getRecipientEmail()))
                            {{ $card->getOrderType() }} to {{ $card->getRecipientEmail() }}
                        @else
                            {{ $card->getOrderType() }}
                        @endif
                    </td>
                    <td data-title="Amount">{{ $card->getCardAmount()->getAmount() }}</td>
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
    </div>
</div>
@stop

@section('script-footer')
<script type="text/javascript">
    $(document).ready(function () {
        $('#track-cards').dataTable({
            'searching': true
        });
    });
</script>
@stop
