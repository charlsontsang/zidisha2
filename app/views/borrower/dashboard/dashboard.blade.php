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
<div class="panel panel-info">
    <div class="panel-body">
    	@lang('borrower.dashboard.pending-confirmation', ['resendLink' => link_to_route('borrower:resend:verification')])
    </div>
<div>
@endif

@if($volunteerMentor)
    @include('borrower.dashboard.partials.volunteer-mentor', compact('volunteerMentor'))
@endif

@include('borrower.dashboard.partials.feedback', compact('feedbackMessages'))

@include('borrower.dashboard.partials.do-more')

@stop
