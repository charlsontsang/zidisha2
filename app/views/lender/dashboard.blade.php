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
    		<img src="/assets/images/test-photos/profile1.jpg" width="100%">
    		<h2>jkurnia</h2>
	    	<a href="#">View profile</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#">Edit profile</a>
		</div>
	</div>
	<div class="col-sm-8">
		<div class="div-header">
		    <h2>Lending Account</h2>
		</div>

		<div class="row">
			<div class="col-xs-12 col-sm-6 pull-right">
		    	<div class="text-light">
		    		You've leveraged $XX in funds uploaded to make <strong>$XX</strong> worth of loans!
		    		<br/><br/>
		    	</div>
		    </div>

		    <div class="col-xs-6 col-sm-4">
		        <p>Funds uploaded:</p>
		        <p>Total amount lent:</p>
		        <p>Lending credit available:</p>
		    </div>

		    <div class="col-xs-6 col-sm-2">
		        <p>$XX</p>
		        <p>$XX</p>
		        <p>$XX.XX</p>
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
		            TO DO
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
		            TO DO
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

		    <div class="col-xs-6 col-sm-2">
		        <p>
		            None yet!
		            <!-- TO DO: if the lender isn't a member of any group, display none yet! and the button, otherwise hide it and display the groups -->
		        </p>
		    </div>
		    <div class="col-xs-6 col-xs-offset-6 col-sm-6 col-sm-offset-0">
		        <p>
			        <a href="{{ route('lender:groups') }}">
			            Join a group
			        </a>
			    </p>
		    </div>
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

		    <div class="col-xs-6 col-sm-2">
		        <p>$XX</p>
		        <p><i class="fa fa-fw fa-plus"></i>$XX</p>
		        <p><i class="fa fa-fw fa-plus"></i>$XX</p>
		    </div>
		</div>

		<div class="row">
		    <div class="col-xs-6 col-sm-5">
		        <h2>
		        	Your total impact: 
		        </h2>
		    </div>

		    <div class="col-xs-6 col-sm-7">
		        <h2>$xx</h2>
		    </div>
		</div>

		<hr/>

	</div> <!-- /col-sm-8 -->
</div>

@stop
