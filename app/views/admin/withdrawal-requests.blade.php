@extends('layouts.master')

@section('page-title')
Withdrawal Requests
@stop

@section('content')
<table class="table table-striped">
    <thead>
    <tr>
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
    {{ BootstrapForm::open(['route' => ['admin:post:withdrawal-requests', $request->getId()]]) }}
    {{ BootstrapForm::populate($form) }}

    <tr>
        <td>{{ $request->getCreatedAt()->format('d-m-Y') }}</td>
        <td><p><a href="{{ route('lender:public-profile', $request->getLender()->getUser()->getUserName()) }}">
                {{ $request->getLender()->getName() }}</a></p>
        <p>{{ $request->getLender()->getUser()->getEmail() }}</p>
        </td>
        <td><p>Uploaded: </p>
            <p>Repaid: </p>
            <p>Withdrawn: </p>
        </td>
        <td>{{ $request->getPaypalEmail() }}</td>
        <td>{{ $request->getAmount() }}</td>
        <td>
            @if($request->isPaid())
                Paid
            @else
            {{ BootstrapForm::submit('Pay') }}
            @endif
        </td>
    </tr>
    {{ BootstrapForm::close() }}
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->links() }}

@stop

