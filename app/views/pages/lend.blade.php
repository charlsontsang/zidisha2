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
                        {{ $selectedLoanCategory ? $selectedLoanCategory->getName() : 'All' }}
                        <i class="fa fa-fw fa-caret-down"></i>
                    </div>
                    <span class="text">projects in</span>
                    <div class="btn btn-default btn-filter" target="#filter-countries">
                        {{ $selectedCountry ? $selectedCountry->getName() : 'All Countries' }}
                        <i class="fa fa-fw fa-caret-down"></i>
                    </div>
                    <span class="text">sorted by</span>
                    <div class="btn btn-default btn-filter" target="#filter-sortings">
                        Recently Added
                        <i class="fa fa-fw fa-caret-down"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <form role="search" action="{{ route('lend:index', $searchRouteParams) }}" method="get">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search" name="search" value="{{{ $searchQuery }}}">
                        <div class="input-group-btn">
                            <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                        </div>
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

        @if($selectedLoanCategory)
        <h1>{{ $selectedLoanCategory->getName(); }}</h1>

        <p><strong>How it works: </strong> {{ $selectedLoanCategory->getHowDescription() }} </p>

        <p><strong>Why it's important: </strong> {{ $selectedLoanCategory->getWhyDescription() }} </p>

        <p><strong>What your loan can do: </strong> {{ $selectedLoanCategory->getWhatDescription() }}
        </p>
        <hr/>
        @endif

        <p>
            We found 
            <strong>{{ $countResults }} {{ $selectedLoanCategory ? $selectedLoanCategory->getName() : '' }} projects</strong>@if($selectedCountry) in {{ $selectedCountry->getName(); }}@endif.
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            @if($countAll > $countResults)
            <a href="{{ route('lend:index', ['category' => 'all'] + ['country' => 'everywhere'] + $routeParams) }}">View all {{ $countAll }} projects</a>
            @endif
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
        <strong>All Countries</strong>
        @else
        <li>
            <a href="{{ route('lend:index', ['country' => 'everywhere'] + $routeParams) }}"> All Countries </a>
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

