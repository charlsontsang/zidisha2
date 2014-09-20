@extends('layouts.side-menu-simple')

@section('page-title')
Following
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.lender-links')
@stop

@section('page-content')

<p>  
    Stay up-to-date on the progress of your favorite entrepreneurs.
    The borrowers you’ve funded and opted to follow are listed below.
    Use the buttons and check boxes next to their names to get real-time comment updates
    and receive notifications when they post new loan projects.
</p>
@if (!empty($followingFollowers))
	<p>
	    P.S. To receive notifications for entrepreneurs you have not yet funded,
	    simply click on the “FOLLOW” button in their profiles.
	    They’ll automatically get added to this page!
	    You can unsubscribe at any time using the "UNFOLLOW" button.
	</p>
@endif

<br/>

@if (!empty($followingFollowers))
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">
                Entrepreneurs You're Following
            </h3>
        </div>
        <div class="panel-body">
			@include('lender.follow.followers', ['followers' => $followingFollowers])
        </div>
    </div>
@endif

<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">
            Entrepreneurs You've Funded
        </h3>
    </div>
    <div class="panel-body">
    	@if (!empty($fundedFollowers))
			@include('lender.follow.followers', ['followers' => $fundedFollowers])
		@else
    		After you lend, you can set your follow preference for each entrepreneur you fund here. <a href="{{ route('lender:edit-profile') }}" class="btn btn-primary pull-right">Make a loan</a>
    	@endif
    </div>
</div>

@stop
