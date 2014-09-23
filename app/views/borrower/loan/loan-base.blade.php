@extends('layouts.side-menu-simple')

@section('page-title')
    {{ $loan->getSummary() }}
@stop

@section('menu-title')
    @lang('borrower.menu.links-title')
@stop

@section('menu-links')
    @include('partials.nav-links.borrower-links')
@stop


@section('page-content')
@stop
