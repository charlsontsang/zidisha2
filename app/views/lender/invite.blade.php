@extends('layouts.master')

@section('page-title')
Invite Your Friends To Zidisha
@stop

@section('content')
<div class="wrapper" style="text-align: center;">
    <h2>
        Send a friend $25 in free lending credit!
    </h2>
</div>

<div>
    <div style="text-align: center;margin-top: 20px;">
        <div>
            You'll receive a matching $25 credit when they lend.
            &nbsp;&nbsp;&nbsp;&nbsp;
            <a href="{{ route('lender:how-it-works') }}">How it works</a>
        </div>
        <br/>
        <br/>

        <div>
            {{ BootstrapForm::open(array('route' => 'lender:post-invite')) }}
            {{ BootstrapForm::populate($form) }}

            {{ BootstrapForm::text('emails', null, ['placeholder' => 'Enter emails separated by commas']) }}
            {{ BootstrapForm::text('subject') }}
            {{ BootstrapForm::textarea('note', null, ['placeholder' => 'Add A note']) }}
            {{ BootstrapForm::submit('submit') }}

            {{ BootstrapForm::close() }}
        </div>
        <div>
            OR
        </div>

        <div>
            <input size="50 px" type="text" value="{{ $invite_url }}" readonly="">
        </div>
        <p>
            Share or tweet this link
                            <span class="share-actions">
                                &nbsp;&nbsp;
                                <a href="{{$facebook_url}}">
                                    <img src="{{ asset('/assets/images/icons/fb.png') }}" alt="Share on Facebook"/>
                                </a>
                                <a href="{{$twitter_url}}">
                                    <img src="{{ asset('/assets/images/icons/twitter.png') }}" alt="Tweet"/>
                                </a>
                            </span>
        </p>
    </div>

    <h3 style="margin-bottom: 25px">
        <span style="color:#00aeef;">{{$count_joined_invites}}</span> of your {{$count_invites}} invitees have joined
        Zidisha.
    </h3>

    <table class="table table-striped">
        <thead>
        <tr>
            <th class="td-profile-image"></th>
            <th>Date Invited</th>
            <th>Email</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invites as $invite)
        <tr>
            <td>@if($invite->getInvitee())
                <a href="{{ route('lender:public-profile', $invite->getInvitee()->getUser()->getUsername()) }} ">
                    {{ $invite->getInvitee()->getUser()->getUsername()}}
                </a>
                @endif
            </td>
            <td>{{ $invite->getCreatedAt()->format('d-m-Y') }}</td>
            <td>{{ $invite->getEmail() }}</td>
            <td>
                @if($invite->getInvitee())
                Joined on {{ $invite->getInvitee()->getCreatedAt()->format('d-m-Y') }}
                @else
                Invite Not Yet Accepted
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
</div>

@stop
