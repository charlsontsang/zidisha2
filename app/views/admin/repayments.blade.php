@extends('layouts.master')

@section('page-title')
Exchange rates
@stop

@section('content')
<div class="page-header">
    <h1>Enter Repayments</h1>
</div>

<h3>Upload Repayments</h3>

{{ BootstrapForm::open(array('controller' => 'AdminController@postEnterRepayment', 'translationDomain' => 'repayments', 'files' => true)) }}
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
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::select('country', $form->getCountries(), Request::query('country')) }}
{{ BootstrapForm::select('status', $form->getStatus(), Request::query('status')) }}
{{ BootstrapForm::text('search', Request::query('search')) }}
{{ BootstrapForm::submit('Search') }}

{{ BootstrapForm::close() }}

@stop
