@extends('layouts.side-menu')

@section('page-title')
Enter Repayments
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.staff-links')
@stop

@section('page-content')
<h4>Upload Repayments Spreadsheet</h4>

{{ BootstrapForm::open(array('route' => 'admin:upload-repayments', 'files' => true)) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::select('countryCode', $form->getCountrySlug(), 'null', ['label' => 'Choose country']) }}
{{ BootstrapForm::file('inputFile', ['label' => 'Choose spreadsheet']) }}

{{ BootstrapForm::submit('Upload Repayments') }}

{{ BootstrapForm::close() }}

<br>

<br/>
<a href="{{ route('admin:repayment-process', \Zidisha\Borrower\Borrower::PAYMENT_COMPLETE) }}"> Ready to Process </a>: {{ $paymentCounts['complete'] }}<br/>
<a href="{{ route('admin:repayment-process', \Zidisha\Borrower\Borrower::PAYMENT_INCOMPLETE) }}"> Incomplete </a>: {{ $paymentCounts['incomplete'] }}<br/>
<a href="{{ route('admin:repayment-process', \Zidisha\Borrower\Borrower::PAYMENT_FAILED) }}"> Failed </a>: {{ $paymentCounts['failed'] }}<br/>
<a href="{{ route('admin:repayments-refunds') }}"> Refunds </a>: {{  $paymentCounts['refunds'] }}<br/>

<hr/>

<h4>Enter Repayment Manually</h4>

{{ BootstrapForm::open(array('route' => 'admin:repayments', 'method' => 'get')) }}
{{ BootstrapForm::populate($filterForm) }}

{{ BootstrapForm::select('country', $filterForm->getCountries(), Request::query('country'), ['label' => 'Choose country']) }}
{{ BootstrapForm::text('search', Request::query('search'), ['label' => 'Search for name, phone or email']) }}
{{ BootstrapForm::submit('Find Borrower') }}

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
            <a href="{{ route('admin:repayment-schedule', $borrower->getId()) }}">View Repayment Schedule</a>
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
