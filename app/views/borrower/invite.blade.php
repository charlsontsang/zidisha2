@extends('layouts.master')

@section('page-title')
Send Invites
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
            <h1>Send Invites</h1>
        </div>

        @if($isEligible == 1)
         <div>
            {{ BootstrapForm::open(array('route' => 'borrower:post-invite')) }}
            {{ BootstrapForm::populate($form) }}

            {{ BootstrapForm::text('email') }}
            {{ BootstrapForm::text('borrowerName') }}
            {{ BootstrapForm::text('borrowerEmail') }}
            {{ BootstrapForm::text('subject') }}
            {{ BootstrapForm::textarea('note', null, ['style' => 'height:100px'], ['placeholder' => 'Add a note']) }}
            {{ BootstrapForm::submit('Send Invite', ['class' => 'btn btn-primary']) }}

            {{ BootstrapForm::close() }}
        </div>
        @else
            {{ \Lang::get('borrower.invite.not-eligible') }}

            @if($isEligible == 2)
                {{ \Lang::get('borrower.invite.not-eligible-repayRate') }}
            @elseif($isEligible == 3)
                {{ \Lang::get('borrower.invite.not-eligible-invitee-repayRate') }}
            @else
                {{ \Lang::get('borrower.invite.not-eligible-invitee-quota', ['maxInviteesWithoutPayment' => $maxInviteesWithoutPayment, 'myInvites' => route('borrower:invites')]) }}
            @endif
        @endif
    </div>
</div>
@stop
