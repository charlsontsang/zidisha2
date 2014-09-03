@extends('layouts.side-menu')

@section('page-title')
Dashboard
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.staff-links')
@stop

@section('page-content')
<h2>Settings</h2>
<a href="{{ route('admin:exchange-rates') }}">2. Exchange Rates </a><br/>
<a href="{{ route('admin:repayments') }}"> Enter Repayments </a>
@stop
