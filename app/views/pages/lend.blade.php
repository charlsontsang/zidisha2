@extends('layouts.master')

@section('page-title')
@lang('lend.page-title')
@stop

@section('content')
<div class="page-header">
    <h1>@lang('lend.page-header')</h1>
</div>

<div class="row">
    <div class="col-xs-4">
        <form class="form-inline" role="form" action="{{ route('lend:index', $searchRouteParams) }}" method="get">
            <div class="form-group">
                <label class="sr-only" for="search">Email address</label>
                <input type="text" class="form-control" id="search" placeholder="Search" name="search"
                       value="{{{ $searchQuery }}}">
            </div>
            <button type="submit" class="btn btn-default">Go</button>
        </form>

        <h2>@lang('lend.sidebar.category-heading')</h2>

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

        <h2>@lang('lend.sidebar.country-heading')</h2>

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

    <div class="col-xs-8">
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

        @foreach($paginator as $loan)
        <div class="media">

            <a class="pull-left"
               href="{{ route('borrower:public-profile', $loan->getBorrower()->getUser()->getUsername()) }}"><img
                    src="{{ $loan->getBorrower()->getUser()->getProfilePictureUrl() }}" width="100" height="100"></a>

            <div class="media-body">
                <ul class="list-unstyled">
                    <li>
                        <a href="{{ route('loan:index', $loan->getId()) }}"><h2>{{ $loan->getSummary() }}</h2></a>

                        <p>{{ $loan->getProposal() }}</p>
                        <strong>@lang('lend.loan.amount'): </strong> {{ $loan->getAmount() }} USD
                        <strong>@lang('lend.loan.interest-rate'): </strong> {{ $loan->getInterestRate() }} %
                        @include('partials/_progress', [ 'raised' => rand(1,100)])
                    </li>
                    <br>
                </ul>
            </div>
        </div>
        @endforeach
        {{ $paginator->appends(['search' => $searchQuery])->links() }}
    </div>
</div>
@stop

