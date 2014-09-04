@extends('layouts.side-menu')

@section('page-title')
{{ \Lang::get('borrower.loan-application.current-credit.title') }}
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stop

@section('page-content')
{{ $beginning }}
{{ $note }}
{{ $inviteCredit }}
{{ $volunteerMentorCredit }}
{{ $end }}
@stop
