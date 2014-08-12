@extends('layouts.master')

@section('page-title')
Invite Your Friends
@stop

@section('content')
@if($isEligible == 1)
    <div class="page-header" style="text-align: center;">
        <h1>Invite your Friend to Zidisha!</h1>
    </div>

    <br/><br/>
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
        {{ \Lang::get('borrower.invite.not-eligible-invitee-quota', ['maxInviteesWithoutPayment' => $maxInviteesWithoutPayment]) }}
    @endif
@endif
@stop
