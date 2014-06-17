@extends('layouts.master')

@section('page-title')
Funds
@stop

@section('content')
<div class="page-header">
    <h1>Add or Withdraw Funds</h1>
</div>


<div >
    <strong>Balance available: USD {{ $currentBalance }} </strong>
</div>

<div>
    <strong>Add Funds</strong>
</div>

{{ BootstrapForm::open(array('route' => '', 'translationDomain' => 'fund')) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::text('creditAmount') }}
{{ BootstrapForm::text('donationAmount') }}

{{ BootstrapForm::hidden('feeAmount') }}
{{ BootstrapForm::hidden('totalAmount') }}

{{ BootstrapForm::label("Total amount to be charged to your account: USD ") }}

{{ BootstrapForm::submit('save') }}

{{ BootstrapForm::close() }}

@stop
