<li><a href="{{ route('lender:dashboard') }}">Dashboard</a></li>
<li><a href="{{ route('lender:loans') }}">Your Loans</a></li>
<li><a href="{{ route('lender:following') }}">Following</a></li>
<li><a href="{{ route('lender:public-profile', Auth::getUser()->getUsername()) }}">View Profile</a></li>
<li><a href="{{ route('lender:edit-profile') }}">Edit Profile</a></li>
<li><a href="{{ route('lender:preference') }}">Account Preferences</a></li>
<li><a href="{{ route('lender:gift-cards') }}">Gift Cards</a></li>
<li><a href="{{ route('lender:gift-cards:track') }}">Track Gift Cards</a></li>
<li><a href="{{ route('lender:invite') }}">Invite Friends</a></li>
<li><a href="{{ route('lender:history') }}">Transaction History</a></li>
<li><a href="{{ route('lender:funds') }}">Transfer Funds</a></li>
<li><a href="{{ route('lender:auto-lending') }}">Autolending</a></li>
<!-- TO DO: move this logic to a controller -->
<?php
$lendingGroups = Zidisha\Lender\LendingGroupQuery::create()->getLendingGroupsForLender(\Auth::user()->getLender());
?>
@if (count($lendingGroups)>0)
    @foreach($lendingGroups as $lendingGroup)
        <li><a href="{{ route('lender:group', $lendingGroup->getId()) }}">{{ $lendingGroup->getName() }}</a></li>
    @endforeach
@else
        <li><a href="{{ route('lender:groups') }}">Lending Groups</a></li>
@endif