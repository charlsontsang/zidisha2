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
<p>
    Total lenders : {{ $totalLenders }}
    <br/>
    Active lenders : {{ $activeLenders }}
    <br/>
    Logged in during past 2 months : {{ $activeLendersInPastTwoMonths }}
    <br/>
    Number of lenders using automated lending : {{ $lenderUsingAutomatedLending }}
    <br/>
    Total lender credit available : {{ $totalLenderCredit }}
</p>
@stop
