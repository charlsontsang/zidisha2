@extends('layouts.side-menu-simple')

@section('page-title')
Dashboard
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
    <div class="alert alert-warning">
    	@lang('borrower.dashboard.pending-confirmation', ['resendLink' => link_to_route('borrower:resend:verification')])
    </div>
@endif

@if($volunteerMentor)
    @include('borrower.dashboard.volunteer-mentor', compact('volunteerMentor'))
@endif

@include('borrower.dashboard.feedback', compact('feedbackMessages'))

@include('borrower.dashboard.do-more')

@stop
