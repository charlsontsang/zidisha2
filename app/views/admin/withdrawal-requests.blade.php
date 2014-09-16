@extends('layouts.master')

@section('page-title')
Withdraw Requests
@stop

@section('content')
<div class="page-header">
    <h1>Withdraw Requests</h1>
</div>
@if(Auth::getUser()->isAdmin())
    {{ BootstrapForm::open(['route' => ['admin:post:paypal-withdrawal-requests'], 'id' => 'paypal-mass-payment-form']) }}
    {{ BootstrapForm::populate($form) }}
    <button id="paypal-mass-payment" class="btn btn-primary" type="submit">Process payments</button>
    {{ BootstrapForm::close() }}
@endif
<table class="table table-striped">
    <thead>
    <tr>
        <th></th>
        <th>Date</th>
        <th>Lender</th>
        <th>Cumulative Amount</th>
        <th>PayPal Email</th>
        <th>Withdrawal Amount</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    @foreach($paginator as $request)

    <tr>
        <td>
            @if(!$request->isPaid())
            {{ BootstrapForm::checkbox('ids['.$request->getId().']', $request->getId(), null, ['class' => 'withdraw-checkbox']) }}
            @endif
        </td>
        <td>{{ $request->getCreatedAt()->format('d-m-Y') }}</td>
        <td><p><a href="{{ route('lender:public-profile', $request->getLender()->getUser()->getUserName()) }}">
                {{ $request->getLender()->getName() }}</a></p>
        <p>{{ $request->getLender()->getUser()->getEmail() }}</p>
        </td>
        <td><p>Uploaded: {{ $uploaded[$request->getLenderId()] }}</p>
            <p>Repaid: {{ $repaid[$request->getLenderId()] }}</p>
            <p>Withdrawn: {{ $withdrawn[$request->getLenderId()]->multiply(-1) }}</p>
        </td>
        <td>{{ $request->getPaypalEmail() }}</td>
        <td>{{ $request->getAmount() }}</td>
        <td>
            @if(Auth::getUser()->isAdmin())
                {{ BootstrapForm::open(['route' => ['admin:post:withdrawal-requests', $request->getId()]]) }}
                {{ BootstrapForm::populate($form) }}
                @if($request->isPaid())
                    Paid
                @else
                {{ BootstrapForm::submit('Pay') }}
                @endif
            @else
                @if($request->isPaid())
                    Paid
                @else
                    Not yet paid
                @endif
            @endif
        </td>
    </tr>
    {{ BootstrapForm::close() }}
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->links() }}
@stop

@section('script-footer')
<script type="text/javascript">
    $(function() {
        $('#paypal-mass-payment-form').submit(function() {
            if (!confirm('Are you sure you want to process the selected withdrawal requests?')) {
                return false;
            }
            $('.withdraw-checkbox').clone().appendTo($(this)).hide();
        });
    });
</script>
@stop
