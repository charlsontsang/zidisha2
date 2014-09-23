@extends('layouts.master')

@section('page-title')
Lend
@stop

@section('content-top')
<div id="mobile-results-desc">
    <p>
        {{ $countResults }} {{ $selectedLoanCategory ? $selectedLoanCategory->getName() : '' }} Project{{ $countResults == 1 ? '' : 's' }}
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="#" id="more-projects" data-toggle="collapse" data-target="#toggle-moreProjects" data-toggle-text="Hide Filter">
            @if($countAll > $countResults)
                View More
            @else
                Filter Results
            @endif
        </a>
    </p>
</div>
<div id="toggle-moreProjects" class="page-section-filter collapse">
    <div class="container">
        <ul class="filter-bar">
            <li class="text" id="show">Show</li>
            <li class="btn btn-default btn-filter" target="#filter-categories">
                {{ $selectedLoanCategory ? $selectedLoanCategory->getName() : 'All' }}
                <i class="fa fa-fw fa-caret-down"></i>
            </li>
            <li class="text">projects in</li>
            <li class="btn btn-default btn-filter" target="#filter-countries">
                {{ $selectedCountry ? $selectedCountry->getName() : 'All Countries' }}
                <i class="fa fa-fw fa-caret-down"></i>
            </li>
            <li class="text">sorted by</li>
            <li class="btn btn-default btn-filter" target="#filter-sortings">
               {{ $sortBy ? $sortConditions[$sortBy] : 'Recently Added' }}
                <i class="fa fa-fw fa-caret-down"></i>
            </li>
            <li>
                <form role="search" action="{{ route('lend:index', $searchRouteParams) }}" method="get">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search" name="search" value="{{{ $searchQuery }}}">
                        <input type="hidden" name="sortBy" value="{{{ $sortBy }}}">
                        <div class="input-group-btn">
                            <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                        </div>
                    </div>
                </form>
            </li>
        </ul>
    </div>
</div>
@stop

@section('content')
<div id="results-info" class="row">
    <div class="col-sm-12">
        <p>
            <span id="results-desc">
                We found 
                <strong>{{ $countResults }} {{ $selectedLoanCategory ? $selectedLoanCategory->getName() : '' }} project{{ $countResults == 1 ? '' : 's' }}.</strong>
            </span>
    
            @if($countAll > $countResults)
                <span id="view-all">
                    <a href="{{ route('lend:index', $viewAllRouteParams) }}">View all {{ $countAll }} projects</a>
                </span>
            @endif

            @if($selectedLoanCategory)
                @if($selectedLoanCategory->getHowDescription())
                    <span>
                    <a href="#" id="about-category" data-toggle="collapse" data-target="#toggle-aboutCategory" data-toggle-text="Hide description">
                        About {{ $selectedLoanCategory->getName() }}
                    </a>
                    </span>
                @endif
            @endif
        </p>

        @if($selectedLoanCategory)
            @if($selectedLoanCategory->getHowDescription())

            <div id="toggle-aboutCategory" class="collapse">            
            
                <div class="page-header">
                    <h3>{{ $selectedLoanCategory->getName(); }}</h3>
                </div>

                <div class="row">

                    <div class="col-md-6">
                        <p><strong>How it works: </strong> {{ $selectedLoanCategory->getHowDescription() }} </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Why it's important: </strong> {{ $selectedLoanCategory->getWhyDescription() }} </p>

                        <p><strong>What your loan can do: </strong> {{ $selectedLoanCategory->getWhatDescription() }}
                        </p>
                    </div>
                </div>
                <hr/>
            </div>
            @endif
        @endif

    </div>
</div>

<div class="row" style="padding-right: 5px; padding-left: 5px;">
    @foreach($paginator as $loan)
    <div class="col-sm-6 col-md-4" style="padding: 10px;">
        <div class="result">
            <div class="row">
                <div class="col-xs-12">
                    <a class="pull-left profile-image" href="{{ route('loan:index', $loan->getId()) }}"
                        style="background-image:url(/assets/images/test-photos/esther.JPG)">
                <!--
                        <img src="{{ $loan->getBorrower()->getUser()->getProfilePictureUrl() }}">
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

                    <div id="funding-tag">
                        <span><strong>${{ ceil($loan->getStillNeededUsdAmount()->getAmount()) }}</strong></span>
                        <br/>
                        <span class="text-light">Needed</span>
                    </div>
                    
                    <div class="lend-title">
                        <span id="country" class="text-light">
                            {{ $loan->getBorrower()->getCountry()->getName() }}
                        </span>
                        <h2 class="alpha">
                            <a href="{{ route('loan:index', $loan->getId()) }}">
                                {{ $loan->getSummary() }}
                            </a>
                        </h2>
                    </div>
                    
                    <p class="text-light">
                        <i class="fa fa-fw fa-user"></i>
                        {{ $loan->getBorrower()->getName() }}
                        <br/>
                        <i class="fa fa-fw fa-map-marker"></i>
                        {{ $loan->getBorrower()->getProfile()->getCity() }},
                        {{ $loan->getBorrower()->getCountry()->getName() }}
                    </p>

                    @include('loan/partials/progress', [ 'loan' => $loan ])
                </div>
            </div>
        </div>
    </div>
@endforeach
</div>

<div class="row">
    <div class="col-xs-12">
        {{ $paginator->appends(['search' => $searchQuery, 'sortBy' => $routeParams['sortBy']])->links() }}
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

<div id="filter-sortings" class="hide">
    <ul class="list-unstyled">
        @foreach($sortConditions as $key=>$sortCondition)
        <li>
            @if($sortBy == $key)
            <strong>{{ $sortCondition }}</strong>
            @else
            <a href="{{ route('lend:index', ['sortBy' => $key] + $routeParams) }}"> {{
                $sortCondition }} </a>
            @endif
        </li>
        @endforeach
    </ul>
</div>
@stop

@section('script-footer')
<script type="text/javascript">
    $(document).ready(function () {
        $('.moreProjects').click(function () {
            $("#toggle-moreProjects").collapse('toggle');
            return false;
        });
        $('.aboutCategory').click(function () {
            $("#toggle-aboutCategory").collapse('toggle');
            return false;
        });
    });
</script>
@stop
