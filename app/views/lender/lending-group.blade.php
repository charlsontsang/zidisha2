@extends('layouts.master')

@section('page-title')
{{ $group->getName() }}
@stop

@section('content-top')
    <div class="loan-titlebar">
        <span class="text-light">
            Lending Group
        </span>
        <p class="alpha">
            <strong>{{ $group->getName() }}</strong>
        </p>
    </div>
    <div id="carousel-example-generic" class="carousel">
        <div class="carousel-inner group-image">
            <div class="item active">
                @if($group->getGroupProfilePicture())
                    <img src="{{ $group->getGroupProfilePicture()->getImageUrl('small-profile-picture') }}" alt=""/>
                @else
                    <img src="/assets/images/carousel/mary.jpg" width="300px">
                @endif
                <div class="carousel-caption caption-group">
                    <h3>{{ $group->getName() }}</h3>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-sm-8 loan-body">

        <div class="loan-section">
            <div class="loan-section-title">
                <span class="text-light">Impact</span>
            </div>
            <div class="loan-section-content">
                <p>This month: <strong>{{ $groupImpacts['totalImpactThisMonth'] }}</strong>
                </p>
                <p>Last month: <strong>{{ $groupImpacts['totalImpactLastMonth'] }}</strong>
                </p>
        
                <p>
                    All time: <strong>{{ $groupImpacts['totalImpact'] }}</strong>
                </p>
            </div>
        </div>

        <div class="loan-section">

            <div class="loan-section-title">
                <span class="text-light">About</span>
            </div>
            <div class="loan-section-content">
                <p>{{ $group->getAbout() }}</p>
            </div>

            <hr/>

            <div class="loan-section-title">
                <span class="text-light">Members</span>
            </div>
            <div class="loan-section-content">
                {{ $membersCount }} Members
                @if($membersCount > 0)
                <div class="Members">
                    <a class="previous-loans" id="toggle-btn"
                       data-toggle="collapse" data-target="#toggle-example">View Members</a>

                    <div id="toggle-example" class="collapse">
                        @foreach($members as $member)
                        <p>
                            <a href="{{ route('lender:public-profile', $member->getMember()->getUser()->getUserName()) }}">
                                {{ $member->getMember()->getUser()->getUserName() }}
                            </a>
                            @if($group->isLeader($member->getMember()))
                                <span class="label label-info">Leader</span>
                            @endif
                        </p>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <hr/>

            <div class="loan-section-title">
                <span class="text-light">Discussion</span>
            </div>
            <div class="loan-section-content">
                <p></p>
            </div>
        </div>

        @include('partials.comments.comments', ['comments' => $comments, 'controller' => 'LendingGroupCommentController', 'canPostComment' => $canPostComment, 'canReplyComment' => $canReplyComment])
            
    </div>

    <div class="col-xs-4">
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
            @if($group->isLeader(Auth::User()->getLender()))
                <a href="{{ route('lender:groups:edit', $group->getId()) }}" class="btn btn-primary">
                    Edit Group
                </a>
            @endif
        </div>
        @endif
        <a href="{{ route('lender:groups') }}">Back to Lending Groups</a>
        <br>
    </div>
</div>

@stop
