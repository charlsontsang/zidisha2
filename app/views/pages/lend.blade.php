@extends('layouts.master')

@section('page-title')
Lend
@stop

@section('content-top')
<div class="page-section page-section-filter">
    <div class="container">
        <div class="row">
            <div class="col-md-10">
                <div class="filter-bar">
                    <span class="text" style="padding-left: 0;">Show</span>
                    <div class="btn btn-default btn-filter" target="#filter-categories">
                        {{ $selectedLoanCategory ? $selectedLoanCategory->getName() : 'Featured' }}
                        @if($selectedLoanCategory)
                            <a href="{{ route('lend:index', ['category' => 'featured'] + $routeParams) }}" class="inverted">
                                <i class="fa fa-fw fa-times"></i>
                            </a>
                        @else
                            <i class="fa fa-fw fa-caret-down"></i>
                        @endif
                    </div>
                    <span class="text">projects in</span>
                    <div class="btn btn-default btn-filter" target="#filter-countries">
                        {{ $selectedCountry ? $selectedCountry->getName() : 'All Countries' }}
                        @if($selectedCountry)
                        <a href="{{ route('lend:index', ['country' => 'everywhere'] + $routeParams) }}" class="inverted">
                            <i class="fa fa-fw fa-times"></i>
                        </a>
                        @else
                        <i class="fa fa-fw fa-caret-down"></i>
                        @endif
                    </div>
                    <span class="text">sorted by</span>
                    <div class="btn btn-default btn-filter" target="#filter-sortings">
                        Recently Added
                        <i class="fa fa-fw fa-caret-down"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <form class="form-inline" role="form" action="{{ route('lend:index', $searchRouteParams) }}" method="get">
                    <div class="input-group">
                        <label class="sr-only" for="search">Search</label>
                        <span class="input-group-addon"><i class="fa fa-fw fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search" name="search" value="{{{ $searchQuery }}}">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-sm-12">

        @if($selectedCountry)
        <h2>{{ $selectedCountry->getName(); }}</h2>
        <hr/>
        @endif

        @if($selectedLoanCategory)
        <h2>{{ $selectedLoanCategory->getName(); }}</h2>
        <br>

        <p><strong>@lang('lend.category.how-it-works'): </strong> {{ $selectedLoanCategory->getHowDescription() }} </p>
        <br>

        <p><strong>@lang('lend.category.why-important'): </strong> {{ $selectedLoanCategory->getWhyDescription() }} </p>
        <br>

        <p><strong>@lang('lend.category.what-your-loan-do'): </strong> {{ $selectedLoanCategory->getWhatDescription() }}
        </p>
        <hr/>
        @endif

        <p>
            We found <strong>12 featured projects</strong>.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#">View all 198 projects</a>
        </p>
        <hr/>

    </div>
</div>

<div class="row" style="padding:5px;">
    @foreach($paginator as $loan)
    <div class="col-sm-4" style="padding:10px;">
        <div class="result">
            <div class="row">
                <div class="col-xs-12">
                    <a class="pull-left profile-image" href="{{ route('loan:index', $loan->getId()) }}"
                        style="background-image:url(/assets/images/test-photos/esther.JPG)" width="100%" height="200px">
                <!--
                        <img src="{{ $loan->getBorrower()->getUser()->getProfilePictureUrl() }}" width="100%">
                    -->
                    </a>
                </div>
            </div>
            <div class="row">   
                <div class="col-xs-12 loan">
                    @if($loan->getCategory())
                        <div class="loan-category">
                            {{ $loan->getCategory()->getName() }}
                            @if($loan->getSecondaryCategory())
                            &nbsp;&nbsp;&nbsp;&nbsp;{{ $loan->getSecondaryCategory()->getName() }}
                            @endif
                        </div>
                    @endif
                    
                    <h2 class="alpha" style="height:2em;">
                        <a href="{{ route('loan:index', $loan->getId()) }}">
                            {{ $loan->getSummary() }}
                        </a>
                    </h2>
                    
                    <p>
                        <i class="fa fa-fw fa-user"></i>
                        {{ $loan->getBorrower()->getName() }}
                        <br/>
                        <i class="fa fa-fw fa-map-marker"></i>
                        {{ $loan->getBorrower()->getProfile()->getCity() }},
                        {{ $loan->getBorrower()->getCountry()->getName() }}
                    </p>

                    @include('partials/loan-progress', [ 'loan' => $loan ])
                </div>
            </div>
        </div>
    </div>
@endforeach
</div>

<div class="row">
    <div class="col-xs-12">
        {{ $paginator->appends(['search' => $searchQuery])->links() }}
    </div>
</div>

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

