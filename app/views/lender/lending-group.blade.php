@extends('layouts.master')

@section('page-title')
Lending Groups
@stop

@section('content')
<div class="row">
    <div class="col-xs-7">
        <h2>{{ $group->getName() }}</h2>

        <div class="row">
            <div class="col-xs-5">
                About Group:
            </div>
            <div class="col-xs-7">
                <p>{{ $group->getAbout() }}</p>

                @if(Auth::check() && Auth::getUser()->isLender())
                @if($group->isMember(Auth::User()->getLender()))
                <a href="{{ route('lender:group:leave', $group->getId()) }}" class="btn btn-primary">
                    Leave this group
                </a>
                @else
                <a href="{{ route('lender:group:join', $group->getId()) }}" class="btn btn-primary">
                    Join this group
                </a>
                @endif
                <br><br>
                <div>
                    @if(Auth::User()->getLender()->getId() == $leaderId)
                        <a href="{{ route('lender:groups:edit', $group->getId()) }}" class="btn btn-primary">
                            Edit Group
                        </a>
                    @endif
                </div>
                @endif
                <br>
                {{ $membersCount }} Members
                @if($membersCount > 0)
                <div class="Members">
                    <a class="previous-loans" id="toggle-btn"
                       data-toggle="collapse" data-target="#toggle-example">View Members</a>

                    <div id="toggle-example" class="collapse">
                        @foreach($members as $member)
                        <p><a href="{{ route('lender:public-profile', $member->getMember()->getUser()->getUserName()) }}">{{
                                $member->getMember()->getUser()->getUserName() }}
                            </a>
                            @if($member->getMemberId() == $leaderId)
                            <span class="label label-info">Leader</span>
                            @endif
                        </p>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>

    <div class="col-xs-5">
        <a href="{{ route('lender:groups') }}">Back to Lending Groups</a>
        <br>
        @if($group->getGroupProfilePicture())
            <img src="{{ $group->getGroupProfilePicture()->getImageUrl('small-profile-picture') }}" alt=""/>
        @endif
    </div>
</div>

@stop
