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
@if($isEligible === true)
    {{ BootstrapForm::open(array('route' => 'borrower:post-invite', 'translationDomain' => 'borrower.invite')) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::text('email') }}
    {{ BootstrapForm::text('borrowerName') }}
    {{ BootstrapForm::text('borrowerEmail') }}
    {{ BootstrapForm::text('subject') }}
    {{ BootstrapForm::textarea('note', null, ['style' => 'height:100px']) }}
    {{ BootstrapForm::submit('sendInvite', ['class' => 'btn btn-primary']) }}
    
    {{ BootstrapForm::close() }}
@else
    <p>
    @lang('borrower.invite.not-eligible')

    @if($isEligible == 'insufficientRepaymentRate')
        @lang('borrower.invite.not-eligible-repayRate')
    @elseif($isEligible == 'insufficientInviteesRepaymentRate')
        @lang('borrower.invite.not-eligible-invitee-repayRate')
    @elseif ($isEligible == 'exceedsMaxInviteesWithoutPayment')
        @lang('borrower.invite.not-eligible-max-invites-without-payment', ['maxInviteesWithoutPayment' => $maxInviteesWithoutPayment, 'myInvites' => route('borrower:invites')])
    @elseif ($isEligible == 'noPayments')
        @lang('borrower.invite.not-eligible-no-payments')
    @elseif ($isEligible == 'exceedsInviteeQuota')
        @lang('borrower.invite.not-eligible-invitee-quota')
    @endif
    </p>
@endif
@stop
