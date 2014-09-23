@extends('layouts.side-menu-simple')

@section('page-title')
	@lang('borrower.menu.dashboard')
@stop

@section('menu-title')
	@lang('borrower.menu.links-title')
@stop

@section('menu-links')
    @include('partials.nav-links.borrower-links')
@stop


@section('page-content')

@include('borrower.dashboard.partials.'.$partial, compact('repaymentSchedule'))
        
@if(!$borrower->isVerified())
	@include('borrower.dashboard.partials.pending-confirmation')
@endif

@if($volunteerMentor)
    @include('borrower.dashboard.partials.volunteer-mentor', compact('volunteerMentor'))
@endif

@include('borrower.dashboard.partials.feedback', compact('feedbackMessages'))

@include('borrower.dashboard.partials.do-more')

@stop
