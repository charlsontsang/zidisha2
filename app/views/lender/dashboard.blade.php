@extends('layouts.master')

@section('page-title')
Dashboard
@stop

@section('content')
<div class="row">
  <div class="div-header">
      <h1>Dashboard</h1>
  </div>

  
</div>

<div class="row lending-account">
  <div class="col-md-12">

    <div class="div-header">
      <h2>Lending Account</h2>
    </div>

      <!--<div class="col-xs-12 col-sm-6 pull-right">
        <div class="text-light">
        You've leveraged {{ $totalFundsUpload }} in funds uploaded to make <strong>{{ $totalLentAmount }}</strong> worth of loans!
        <br/><br/>
        </div>
      </div>-->

    <div class="col-md-6 col-sm-4 dashboard-content">
      <div class="text-light">
        You've leveraged {{ $totalFundsUpload }} in funds uploaded to make <strong>{{ $totalLentAmount }}</strong> worth of loans!
      </div>

      <p>Funds uploaded: <em> {{ $totalFundsUpload }} </em></p>
      <p>Total amount lent: <em> {{ $totalLentAmount }} </em> </p>
      <p>Lending credit available: <em> {{ $currentBalance }} </em> </p>
        <a class="btn btn-default" href="{{ route('lend:index') }}" class="lender-dashboard-link">Make a loan</a>

    </div> 

    <div class="col-xs-6 col-md-6 col-sm-offset-0 dashboard-control">
<div class="col-sm-4 pull-right">
  <div class="well" style="text-align: center;">
      <img src="{{ Auth::getUser()->getProfilePictureUrl() }}" width="100%">
      <h2>{{ Auth::getUser()->getUsername() }}</h2>
      <a href="{{ route('lender:public-profile', Auth::getUser()->getUsername()) }}">View profile</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{{ route('lender:edit-profile') }}">Edit profile</a>
  </div>
  </div>    
</div>
</div>

<hr/>

<div class="row network-row">
  <div class="div-header">
    <h2>Network</h2>
  </div>

  <div class="col-md-6 col-sm-4">
    <p>Invites sent: <span>{{ $numberOfInvitesSent }}</span></p>
    <p>Gift cards gifted:<span>{{ $numberOfGiftedGiftCards }}</span></p>
  </div>

  <!--<div class="col-md-6  col-sm-6">
    <a href="{{ route('lender:invite') }}" class="btn btn-primary lender-dashboard-btn">
      Send an invite
    </a>
  </div>-->

  <div class="col-xs-6 col-md-6 ">
    <a class="btn btn-primary" href="{{ route('lender:gift-cards') }}">Give a gift card</a>
    <a class="btn btn-primary" href="{{ route('lender:invite') }}" class="btn btn-primary lender-dashboard-btn">Send an invite</a>
  </div>
</div>

<div class="row">
  <div class="div-header">
    <p>Your lending groups:</p>
  </div>

  <div class="col-md-6 col-sm-8">
    <ul>
      @if (count($lendingGroups)>0)
      @foreach($lendingGroups as $lendingGroup)
        <li><a href="{{ route('lender:group', $lendingGroup->getId()) }}">{{ $lendingGroup->getName() }}</a></li>
      @endforeach
      </ul>
      @else
        <li> None yet! </li>
      </ul>

      <a class="btn btn-primary"  href="{{ route('lender:groups') }}">Join a group</a>
    @endif
</div>

<hr/>

<div class="row">
  <div class="div-header">
    <h2>Impact</h2>
  </div>

  <div class="col-xs-6 col-sm-4">
      <p>Amount lent by you: <em> {{ $totalLentAmount }} </em></p>
      <p>Lent by your invitees:<em><i class="fa fa-fw fa-plus"></i>{{ $totalLentAmountByInvitees }}</em></p>
      <p>Lent by your gift card recipients:<em><i class="fa fa-fw fa-plus"></i>{{ $totalLentAmountByRecipients }}</em></p>
  </div>

  <div class="col-md-8 col-sm-5">
    <h2>
      Your total impact: 
      <em>{{ $totalImpact }}</em>
    </h2>
  </div>

  <!--<div class="col-xs-6 col-sm-3">
          <p>{{ $totalLentAmount }}</p>
          <p><i class="fa fa-fw fa-plus"></i>{{ $totalLentAmountByInvitees }}</p>
          <p><i class="fa fa-fw fa-plus"></i>{{ $totalLentAmountByRecipients }}</p>
  </div>-->
</div>

<!--  <div class="row">
      <div class="col-md-8 col-sm-5">
          <h2>
            Your total impact: 
            <em>{{ $totalImpact }}</em>
          </h2>
      </div>

      <!--<div class="col-xs-6 col-sm-7">
          <h2>{{ $totalImpact }}</h2>
      </div>-->
  </div>-->

  <hr/>
</div>
</div>

@stop
