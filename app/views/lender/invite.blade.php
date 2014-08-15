@extends('layouts.master')

@section('page-title')
Invite Friends
@stop

@section('content')
<div class="row">
    <div class="col-sm-10 col-sm-offset-1">
        <div class="page-header" style="text-align: center;">
            <h1>Send a friend $25 to make a loan for free!</h1>
        </div>

        <p style="text-align: center;">
            You'll receive a matching $25 credit when they lend.
            &nbsp;&nbsp;&nbsp;&nbsp;
            <a href="{{ route('lender:how-it-works') }}">How it works</a>
        </p>

        <br/><br/>

        <div class="row">
            <div class="col-sm-5">

                {{ BootstrapForm::open(array('route' => 'lender:post-invite')) }}
                {{ BootstrapForm::populate($form) }}

                {{ BootstrapForm::text('emails', null, ['placeholder' => 'Enter emails separated by commas']) }}
                {{ BootstrapForm::text('subject') }}
                {{ BootstrapForm::textarea('note', null, ['style' => 'height:100px'], ['placeholder' => 'Add a note']) }}
                {{ BootstrapForm::submit('Send', ['class' => 'btn btn-primary btn-block']) }}

                {{ BootstrapForm::close() }}

            </div>

            <div class="col-sm-2">
                <div class="circle" style="margin-top: 30px; margin-bottom: 30px;">
                    OR
                </div>
            </div>

            <div class="col-sm-5">
                <p>
                    Share or tweet your personal invite link:
                </p>

                <p class="text-large">
                    {{ $invite_url }}
                </p>

                <p>
                    <span class="share-actions">
                        <a href="{{$facebook_url}}">
                            <img src="{{ asset('/assets/images/icons/fb.png') }}" width="50px" style="margin-right: 15px;" alt="Share on Facebook"/>
                        </a>
                        <a href="{{$twitter_url}}">
                            <img src="{{ asset('/assets/images/icons/twitter.png') }}" width="50px" alt="Tweet"/>
                        </a>
                    </span>
                </p>
                
            </div>
        </div>

        <br/><br/>

        <h3 style="margin-bottom: 25px" style="text-align: center;">
            <span style="color:#00aeef;">{{$count_joined_invites}}</span> of your {{$count_invites}} invitees have joined
            Zidisha.
        </h3>

        <table class="table table-striped no-more-tables">
            <thead>
            <tr>
                <th>Date Invited</th>
                <th>Email</th>
                <th>Status</th>
                <th class="td-profile-image"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($invites as $invite)
            <tr>
                <td data-title="Date Invited">{{ $invite->getCreatedAt()->format('M j, Y') }}</td>
                <td data-title="Email">{{ $invite->getEmail() }}</td>
                <td data-title="Status">
                    @if($invite->getInvitee())
                    Joined on {{ $invite->getInvitee()->getCreatedAt()->format('M j, Y') }}
                    @else
                    Invite Not Yet Accepted
                    @endif
                </td>
                <td>@if($invite->getInvitee())
                    <a href="{{ route('lender:public-profile', $invite->getInvitee()->getUser()->getUsername()) }} ">
                        {{ $invite->getInvitee()->getUser()->getUsername()}}
                    </a>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
