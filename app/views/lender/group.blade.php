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
                @if($group->isMember(Auth::getUser()->getLender()))
                <a href="{{ route('lender:group:leave', $group->getId()) }}" class="btn btn-primary">
                    Leave this group
                </a>
                @else
                <a href="{{ route('lender:group:join', $group->getId()) }}" class="btn btn-primary">
                    Join this group
                </a>
                @endif
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
            <img src="{{ $group->getGroupProfilePicture()->getImageUrl('small-profile-picture') }}" alt=""/>
    </div>
</div>

@stop
