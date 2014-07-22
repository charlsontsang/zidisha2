@extends('layouts.master')

@section('page-title')
@lang('lend.page-title')
@stop

@section('content-top')
<div class="page-section page-section-filter">
    <div class="container">
        <div class="row">
            <div class="col-md-10">
                <div class="filter-bar">
                    <span style="padding-left: 0;">Show</span>
                    <div class="btn btn-default btn-filter" target="#filter-categories">
                        {{ $selectedLoanCategory ? $selectedLoanCategory->getName() : 'All' }}
                        @if($selectedLoanCategory)
                            <a href="{{ route('lend:index', ['category' => 'all'] + $routeParams) }}" class="inverted">
                                <i class="fa fa-fw fa-times"></i>
                            </a>
                        @else
                            <i class="fa fa-fw fa-caret-down"></i>
                        @endif
                    </div>
                    <span>projects from</span>
                    <div class="btn btn-default btn-filter" target="#filter-countries">
                        {{ $selectedCountry ? $selectedCountry->getName() : 'Everywhere' }}
                        @if($selectedCountry)
                        <a href="{{ route('lend:index', ['country' => 'everywhere'] + $routeParams) }}" class="inverted">
                            <i class="fa fa-fw fa-times"></i>
                        </a>
                        @else
                        <i class="fa fa-fw fa-caret-down"></i>
                        @endif
                    </div>
                    <span>sorted by</span>
                    <div class="btn btn-default btn-filter" target="#filter-sortings">
                        Repayment Rate
                        <i class="fa fa-fw fa-times"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <form class="form-inline" role="form" action="{{ route('lend:index', $searchRouteParams) }}" method="get">
                    <div class="form-group">
                        <label class="sr-only" for="search">Search</label>
                        <input type="text" class="form-control" placeholder="Search" name="search" value="{{{ $searchQuery }}}" style="width: 100%">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <ul class="nav nav-tabs">
            @foreach(['fund-raising' => 'Fund Raising', 'active' => 'Active', 'completed' => 'Completed'] as $stage =>
            $loanTitle)
            @if($stage == $routeParams['stage'])
            <li class="active"><a href="{{ route('lend:index', ['stage' => $stage] + $routeParams) }}">{{ $loanTitle
                    }}</a></li>
            @else
            <li><a href="{{ route('lend:index', ['stage' => $stage] + $routeParams) }}">{{ $loanTitle }}</a></li>
            @endif
            @endforeach
        </ul>

        @if($selectedLoanCategory)
        <h2>{{ $selectedLoanCategory->getName(); }}</h2>
        <br>

        <p><strong>@lang('lend.category.how-it-works'): </strong> {{ $selectedLoanCategory->getHowDescription() }} </p>
        <br>

        <p><strong>@lang('lend.category.why-important'): </strong> {{ $selectedLoanCategory->getWhyDescription() }} </p>
        <br>

        <p><strong>@lang('lend.category.what-your-loan-do'): </strong> {{ $selectedLoanCategory->getWhatDescription() }}
        </p>
        @endif


        @if($selectedCountry)
        <h2>{{ $selectedCountry->getName(); }}</h2>
        <br>
        @endif
    </div>
</div>

<style>
    .loan-category {
        margin-bottom: 12px;
        font-size: 12px;
        font-weight: bolder;
        text-transform: uppercase;
    }
    .loan h2 {
        font-size: 32px;
        color: #333;
    }
    .loan h2 a {
        color: #333;
    }
    .loan-summary {
        padding: 12px 0;
    }
    a.link-dark {
        color: #333;
    }
</style>

@foreach($paginator as $loan)
<div class="row">

    <div class="col-md-5">
        <a class="pull-left" href="{{ route('borrower:public-profile', $loan->getBorrower()->getUser()->getUsername()) }}">
            <img src="{{ $loan->getBorrower()->getUser()->getProfilePictureUrl() }}" width="100%">
        </a>
    </div>
    
    <div class="col-md-7 loan">
        @if($loan->getCategory())
            <div class="loan-category">
                {{ $loan->getCategory()->getName() }}
                @if($loan->getSecondaryCategory())
                &nbsp;&nbsp;&nbsp;&nbsp;{{ $loan->getSecondaryCategory()->getName() }}
                @endif
            </div>
        @endif
        
        <h2 class="alpha">
            <a href="{{ route('loan:index', $loan->getId()) }}">
                {{ $loan->getSummary() }}
            </a>
        </h2>
        
        <p>
            {{ $loan->getBorrower()->getName() }}
            <img class="leaf" src="{{ '/assets/images/leaf.png' }}"/>
            {{ $loan->getBorrower()->getProfile()->getCity() }},
            {{ $loan->getBorrower()->getCountry()->getName() }}
        </p>

        <p class="loan-summary">
            <a href="{{ route('loan:index', $loan->getId()) }}" class="link-dark">
                {{ Zidisha\Utility\Utility::truncate($loan->getProposal(), 200, array('exact' => false)) }}
            </a>
            <a href="{{ route('loan:index', $loan->getId()) }}">Read More</a>
        </p>
        
        @include('partials/loan-progress', [ 'loan' => $loan ])
    </div>
</div>
<hr/>
@endforeach

{{ $paginator->appends(['search' => $searchQuery])->links() }}

<div id="filter-categories" class="hide">
    <ul class="list-unstyled">
        @if($selectedLoanCategory == null)
        <strong> All </strong>
        @else
        <li>
            <a href="{{ route('lend:index', ['category' => 'all'] + $routeParams) }}"> All </a>
        </li>
        @endif
        @foreach($loanCategories as $loanCategory)
        <li>
            @if($selectedLoanCategory == $loanCategory)
            <strong>{{ $loanCategory->getName()}}</strong>
            @else
            <a href="{{ route('lend:index', ['category' => $loanCategory->getSlug()] + $routeParams) }}"> {{
                $loanCategory->getName()}} </a>
            @endif
        </li>
        @endforeach
    </ul>
</div>

<div id="filter-countries" class="hide">
    <ul class="list-unstyled">
        @if($selectedCountry == null)
        <strong>Everywhere</strong>
        @else
        <li>
            <a href="{{ route('lend:index', ['country' => 'everywhere'] + $routeParams) }}"> Everywhere </a>
        </li>
        @endif
        @foreach($countries as $country)
        <li>
            @if($selectedCountry == $country)
            <strong>{{ $country->getName()}}</strong>
            @else
            <a href="{{ route('lend:index', ['country' => $country->getSlug()] + $routeParams) }}"> {{
                $country->getName()}} </a>
            @endif
        </li>
        @endforeach
    </ul>
</div>
@stop

