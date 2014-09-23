@extends('layouts.master')

@section('page-title')
Join the global P2P microlending movement
@stop

@section('content')
<div class="page-header">
    <h1>Edit Profile</h1>
</div>


{{ BootstrapForm::open(array('route' => 'test:view:post', 'translationDomain' => 'test')) }}

{{ BootstrapForm::text('amount') }}

{{ BootstrapForm::text('donationAmount') }}

{{ BootstrapForm::text('totalAmount') }}

{{ BootstrapForm::text('paymentMethod') }}

{{ BootstrapForm::text('transactionFee') }}

{{ BootstrapForm::submit('save') }}

{{ BootstrapForm::close() }}

@stop