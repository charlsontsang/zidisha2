@extends('layouts.master')

@section('page-title')
Exchange rates
@stop

@section('content')
<div class="page-header">
    <h1>Enter Repayments</h1>
</div>

<p> Upload Repayments </p>

{{ BootstrapForm::open(array('controller' => 'AdminController@postEnterRepayment', 'translationDomain' => 'enter-repayments', 'files' => true)) }}
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

@stop
