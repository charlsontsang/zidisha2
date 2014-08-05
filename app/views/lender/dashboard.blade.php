@extends('layouts.master')

@section('page-title')
Dashboard
@stop

@section('content')
	<div class="page-header">
	    <h1>Dashboard</h1>
	</div>

<div class="row">
	<div class="col-sm-4 pull-right">
		<div class="well" style="text-align: center;">
    		<img src="{{ Auth::getUser()->getProfilePictureUrl() }}" width="100%">
    		<h2>{{ Auth::getUser()->getUsername() }}</h2>
	    	<a href="{{ route('lender:public-profile', Auth::getUser()->getUsername()) }}">View profile</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{{ route('lender:edit-profile') }}">Edit profile</a>
		</div>
	</div>
	<div class="col-sm-8">
		<div class="div-header">
		    <h2>Lending Account</h2>
		</div>

		<div class="row">
			<div class="col-xs-12 col-sm-6 pull-right">
		    	<div class="text-light">
		    		You've leveraged {{ $totalFundsUpload }} in funds uploaded to make <strong>{{ $totalLentAmount }}</strong> worth of loans!
		    		<br/><br/>
		    	</div>
		    </div>

		    <div class="col-xs-6 col-sm-4">
		        <p>Funds uploaded:</p>
		        <p>Total amount lent:</p>
		        <p>Lending credit available:</p>
		    </div>

		    <div class="col-xs-6 col-sm-2">
		        <p>{{ $totalFundsUpload }}</p>
		        <p>{{ $totalLentAmount }}</p>
		        <p>{{ $currentBalance }}</p>
		    </div>

		    <div class="col-xs-6 col-xs-offset-6 col-sm-6 col-sm-offset-0">
		    	<a href="{{ route('lend:index') }}" class="lender-dashboard-link">
		            Make a loan
		        </a>
		    </div>
		</div>

		<hr/>

		<div class="div-header">
		    <h2>Network</h2>
		</div>

		<div class="row">
		    <div class="col-xs-6 col-sm-4">
		        <p>Invites sent:</p>
		    </div>

		    <div class="col-xs-6 col-sm-2">
		        <p>
		            {{ $numberOfInvitesSent }}
		        </p>
		    </div>
		    <div class="col-xs-6 col-xs-offset-6 col-sm-6 col-sm-offset-0">
		        <a href="{{ route('lender:invite') }}" class="btn btn-primary lender-dashboard-btn">
		            Send an invite
		        </a>
		    </div>
		</div>

		<div class="row">
		    <div class="col-xs-6 col-sm-4">
		        <p>Gift cards gifted:</p>
		    </div>

		    <div class="col-xs-6 col-sm-2">
		        <p>
		            {{ $numberOfGiftedGiftCards }}
		        </p>
		    </div>
		    <div class="col-xs-6 col-xs-offset-6 col-sm-6 col-sm-offset-0">
		        <p>
		        	<a href="{{ route('lender:gift-cards') }}">
		                Give a gift card
		        	</a>
		        </p>
		    </div>
		</div>

		<div class="row">
		    <div class="col-xs-6 col-sm-4">
		        <p>Your lending groups:</p>
		    </div>

		    @if (count($lendingGroups)>0)
		    <div class="col-xs-12 col-sm-8">
		        <p>
					    @foreach($lendingGroups as $lendingGroup)
			                <a href="{{ route('lender:group', $lendingGroup->getId()) }}">{{ $lendingGroup->getName() }}</a>
			                <br/>
			            @endforeach
		        </p>
		    </div>
		    @else
		    <div class="col-xs-6 col-sm-2">
		        <p>
					None yet!
		        </p>
		    </div>
		    <div class="col-xs-6 col-xs-offset-6 col-sm-6 col-sm-offset-0">
		        <p>
			        <a href="{{ route('lender:groups') }}">
			            Join a group
			        </a>
			    </p>
		    </div>
			@endif
		</div>

		<hr/>

		<div class="div-header">
		    <h2>Impact</h2>
		</div>

		<div class="row">
		    <div class="col-xs-6 col-sm-4">
		    	<p>Amount lent by you: </p>
		        <p>Lent by your invitees:</p>
		        <p>Lent by your gift card recipients:</p>
		    </div>

		    <div class="col-xs-6 col-sm-3">
		        <p>{{ $totalLentAmount }}</p>
		        <p><i class="fa fa-fw fa-plus"></i>{{ $totalLentAmountByInvitees }}</p>
		        <p><i class="fa fa-fw fa-plus"></i>{{ $totalLentAmountByRecipients }}</p>
		    </div>
		</div>

		<div class="row">
		    <div class="col-xs-6 col-sm-5">
		        <h2>
		        	Your total impact: 
		        </h2>
		    </div>

		    <div class="col-xs-6 col-sm-7">
		        <h2>{{ $totalImpact }}</h2>
		    </div>
		</div>

		<hr/>

	</div> <!-- /col-sm-8 -->
</div>

@stop
