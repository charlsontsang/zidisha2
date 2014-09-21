@extends('layouts.side-menu')

@section('page-title')
@lang('borrower.vm-guide.vm-guidelines-title')
@stop

@section('menu-title')
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stop

@section('page-content')
@lang('borrower.vm-guide.vm-guidelines-content', ['ethicsLink' => route('page:volunteer-mentor-code-of-ethics'), 'faqLink' => route('page:volunteer-mentor-faq')])
@stop