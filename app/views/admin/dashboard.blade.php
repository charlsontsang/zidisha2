@extends('layouts.master')

@section('page-title')
Dashboard
@stop

@section('content')
<div class="page-header">
    <h1>Dashboard</h1>
</div>

<h2>Setting!</h2>

<a href="{{ route('admin:exchange-rates') }}">2. Exchange Rates </a><br/>
<a href="{{ route('admin:enter-repayment') }}"> Enter Repayments </a>

@stop
