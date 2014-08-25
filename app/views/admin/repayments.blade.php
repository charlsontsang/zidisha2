@extends('layouts.master')

@section('page-title')
Exchange rates
@stop

@section('content')
<div class="page-header">
    <h1>Enter Repayments</h1>
</div>

<h3>Upload Repayments</h3>

{{ BootstrapForm::open(array('route' => 'admin:upload-repayments', 'translationDomain' => 'repayments', 'files' => true)) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::select('countryCode', $form->getCountrySlug()) }}
{{ BootstrapForm::file('inputFile') }}

{{ BootstrapForm::submit('Save') }}

{{ BootstrapForm::close() }}

<br>

<br/>
<a href="{{ route('admin:repayment-process', \Zidisha\Borrower\Borrower::PAYMENT_COMPLETE) }}"> Ready to Process </a>: {{ $paymentCounts['complete'] }}<br/>
<a href="{{ route('admin:repayment-process', \Zidisha\Borrower\Borrower::PAYMENT_INCOMPLETE) }}"> Incomplete </a>: {{ $paymentCounts['incomplete'] }}<br/>
<a href="{{ route('admin:repayment-process', \Zidisha\Borrower\Borrower::PAYMENT_FAILED) }}"> Failed </a>: {{ $paymentCounts['failed'] }}<br/>
<a href="{{ route('admin:repayments-refunds') }}"> Refunds </a>: {{  $paymentCounts['refunds'] }}<br/>

<hr/>

<h3>Find Borrower</h3>

{{ BootstrapForm::open(array('route' => 'admin:repayments', 'method' => 'get')) }}
{{ BootstrapForm::populate($filterForm) }}

{{ BootstrapForm::select('country', $filterForm->getCountries(), Request::query('country')) }}
{{ BootstrapForm::select('status', $filterForm->getStatus(), Request::query('status')) }}
{{ BootstrapForm::text('search', Request::query('search')) }}
{{ BootstrapForm::submit('Search') }}

{{ BootstrapForm::close() }}

@if($borrowers)
<table class="table table-striped">
    <thead>
    <tr>
        <th>Borrower</th>
        <th>Location</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    @foreach($borrowers as $borrower)
    <tr>
        <td>
            <a href="{{ route('admin:borrower', $borrower->getUser()->getId()) }}">
                {{ $borrower->getName() }}
            </a>
            <br/>
            Email: {{ $borrower->getUser()->getEmail() }}<br/>
            Phone: {{ $borrower->getProfile()->getPhoneNumber() }}
        </td>
        <td>
            {{ $borrower->getProfile()->getCity() }}<br/>
            {{ $borrower->getCountry()->getName() }}
        </td>
        <td>
            @if($borrower->getActiveLoanId())
            <a href="{{ ''//route('admin:repayment-schedule') }}">View Repayment Schedule</a>
            @else
            No active loan
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($borrowers)->appends($filterForm->getPaginatorParams())->links() }}
@endif

@stop
