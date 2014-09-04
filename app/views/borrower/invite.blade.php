@extends('layouts.side-menu')

@section('page-title')
Send Invites
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stop

@section('page-content')
@if($isEligible == 1)
    {{ BootstrapForm::open(array('route' => 'borrower:post-invite')) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::text('email') }}
    {{ BootstrapForm::text('borrowerName') }}
    {{ BootstrapForm::text('borrowerEmail') }}
    {{ BootstrapForm::text('subject') }}
    {{ BootstrapForm::textarea('note', null, ['style' => 'height:100px'], ['placeholder' => 'Add a note']) }}
    {{ BootstrapForm::submit('Send Invite', ['class' => 'btn btn-primary']) }}
    
    {{ BootstrapForm::close() }}
@else
    <p>
    {{ \Lang::get('borrower.invite.not-eligible') }}

    @if($isEligible == 2)
        {{ \Lang::get('borrower.invite.not-eligible-repayRate') }}
    @elseif($isEligible == 3)
        {{ \Lang::get('borrower.invite.not-eligible-invitee-repayRate') }}
    @else
        {{ \Lang::get('borrower.invite.not-eligible-invitee-quota', ['maxInviteesWithoutPayment' => $maxInviteesWithoutPayment, 'myInvites' => route('borrower:invites')]) }}
    @endif
    </p>
@endif
@stop
