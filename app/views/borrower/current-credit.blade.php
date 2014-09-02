@extends('layouts.master')

@section('page-title')
{{ \Lang::get('borrower.loan-application.current-credit.title') }}
@stop

@section('content')
<div class="row">
    <div class="col-sm-3 col-md-4">
        <ul class="nav side-menu" role="complementary">
          <h4>Quick Links</h4>
            @include('partials.nav-links.borrower-links')       
          </ul>
    </div>

    <div class="col-sm-9 col-md-8 info-page">
        <div class="page-header">
            <h1>{{ \Lang::get('borrower.loan-application.current-credit.title') }}</h1>
        </div>
	    <p>{{ $beginning }}</p>
	    <p>{{ $note }}</p>
	    <p>{{ $inviteCredit }}</p>
	    <p>{{ $volunteerMentorCredit }}</p>
	    <p>{{ $end }}</p>
	</div>
</div>
@stop
