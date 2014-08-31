@extends('layouts.master')

@section('page-title')
Dashboard
@stop

@section('content')
<div class="row">
    <div class="col-sm-12">
	    <div class="page-header">
	        <h1>Dashboard</h1>
	    </div>
	</div>
	<div class="col-sm-4">
		<h3>Quick Links</h3>
        <p><a href="{{ route('lender:loans') }}">Your Loans</a></p>
        <p><a href="{{ route('lender:public-profile', Auth::getUser()->getUsername()) }}">View Profile</a></p>
        <p><a href="{{ route('lender:edit-profile') }}">Edit Profile</a></p>
        <p><a href="{{ route('lender:preference') }}">Account Preferences</a></p>
        @if (count($lendingGroups)>0)
          @foreach($lendingGroups as $lendingGroup)
            <p><a href="{{ route('lender:group', $lendingGroup->getId()) }}">{{ $lendingGroup->getName() }}</a></p>
	       @endforeach
	    @else
	        <p><a href="{{ route('lender:groups') }}">Lending Groups</a></p>
        @endif
        <p><a href="{{ route('lender:gift-cards') }}">Gift Cards</a></p>
        <p><a href="{{ route('lender:gift-cards:track') }}">Track Gift Cards</a></p>
        <p><a href="{{ route('lender:invite') }}">Invite Friends</a></p>
        <p><a href="{{ route('lender:history') }}">Transaction History</a></p>
        <p><a href="{{ route('lender:funds') }}">Transfer Funds</a></p>
        <p><a href="{{ route('lender:auto-lending') }}">Auto Lending</a></p>
	</div>
	<div class="col-sm-8">
		<h2>Your Project Updates</h2>

		TO DO
	</div>
</div>
@stop
