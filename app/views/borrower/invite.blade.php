@extends('layouts.side-menu')

@section('page-title')
@lang('borrower.menu.send-invites')
@stop

@section('menu-title')
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stop

@section('page-content')
@if($isEligible === true)
    {{ BootstrapForm::open([
        'route' => 'borrower:post-invite',
        'translationDomain' => 'borrower.invite',
        'data-disable-submit' => 'on'])
    }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::text('email') }}
    {{ BootstrapForm::text('borrowerName') }}
    {{ BootstrapForm::text('borrowerEmail') }}
    {{ BootstrapForm::text('subject') }}
    {{ BootstrapForm::textarea('message', null, ['style' => 'height:100px']) }}
    {{ BootstrapForm::submit('send-invite', ['class' => 'btn btn-primary']) }}
    
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
