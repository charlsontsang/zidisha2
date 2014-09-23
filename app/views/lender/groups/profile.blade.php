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
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-sm-8">
        <div class="highlight highlight-panel group">

            <div class="group-title">
                <h1>{{ $group->getName() }}</h1>
                <hr/>
            </div>

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

            <hr/>

            <div class="loan-section">
                <div class="loan-section-title">
                    <span class="text-light">About</span>
                </div>
                <div class="loan-section-content">
                    <p>{{ $group->getAbout() }}</p>
                </div>
            </div>

            <hr/>

            <div class="loan-section">
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
                                <a href="{{ route('lender:public-profile', $member->getMember()->getId()) }}">
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
            </div>

            <hr/>

            <div class="loan-section">
                <div class="loan-section-title">
                    <span class="text-light">Discussion</span>
                </div>
                <div class="loan-section-content">
                    <p></p>
                </div>
            </div>

            @include('partials.comments.comments', ['comments' => $comments, 'controller' => 'LendingGroupCommentController', 'canPostComment' => $canPostComment, 'canReplyComment' => $canReplyComment])
                
        </div>
    </div>

    <div class="col-xs-4">
        @if(!(Auth::check() && Auth::user()->isBorrower()))
            @if($group->isMember(Auth::User()->getLender()))
            <a href="{{ route('lender:group:leave', $group->getId()) }}">
                Leave this group
            </a>
            @else
            <a href="{{ route('lender:group:join', $group->getId()) }}" class="btn btn-primary join-group">
                Join this group
            </a>
            @endif
        <br><br>
        <div>
            @if($group->isLeader(Auth::User()->getLender()))
                <a href="{{ route('lender:groups:edit', $group->getId()) }}">
                    Edit group
                </a>
            @endif
        </div>
        @endif
    </div>
</div>

@if(!(Auth::check() && Auth::user()->isBorrower()))          
    <a href="{{ route('lender:group:join', $group->getId()) }}" class="btn btn-primary btn-block mobile-bottom-btn">
        Join this group
    </a>
@endif

@stop
