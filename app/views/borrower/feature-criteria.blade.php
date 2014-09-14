@extends('layouts.side-menu')

@section('page-title')
@lang('borrower.feature-criteria.title')
@stop

@section('menu-title')
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stop

@section('page-content')
    <div class="info-page">
        @lang('borrower.feature-criteria.content')
    </div>
@stop
