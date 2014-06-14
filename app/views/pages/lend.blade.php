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
        <h2>@lang('lend.sidebar.category-heading')</h2>

        <ul class="list-unstyled">
            <li>
                <a href="{{ route('lend:index', ['category' => 'all'] + $routeParams) }}"> All </a>
            </li>
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
            <li>
                <a href="{{ route('lend:index', ['category' => $routeParams['category']]) }}"> EveryWhere </a>
            </li>
            @foreach($countries as $country)
            <li>
                @if($selectedCountry == $country)
                <strong>{{ $country->getName()}}</strong>
                @else
                <a href="{{ route('lend:index', ['country' => $country->getSlug()] + $routeParams) }}"> {{ $country->getName()}} </a>
                @endif
            </li>
            @endforeach
        </ul>
    </div>

    <div class="col-xs-8">
        @if($selectedLoanCategory)
        <h2>{{ $selectedLoanCategory->getName(); }}</h2>
        <br>

        <p><strong>@lang('lend.category.how-it-works'): </strong> {{ $selectedLoanCategory->getHowDescription() }} </p> <br>

        <p><strong>@lang('lend.category.why-important'): </strong> {{ $selectedLoanCategory->getWhyDescription() }} </p> <br>

        <p><strong>@lang('lend.category.what-your-loan-do'): </strong> {{ $selectedLoanCategory->getWhatDescription() }} </p>
        @endif


        @if($selectedCountry)
        <h2>{{ $selectedCountry->getName(); }}</h2>
        <br>
        @endif

        @foreach($paginator as $loan)
        <div class="media">

            <a class="pull-left" href="{{ route('borrower:public-profile', $loan->getBorrower()->getUser()->getUsername()) }}"><img src="{{ $loan->getBorrower()->getUser()->getProfilePicture() }}" width="100" height="100"></a>
        <div class="media-body">
        <ul class="list-unstyled">
            <li>
                <a href="{{ route('loan:index', $loan->getId()) }}"><h2>{{ $loan->getSummary() }}</h2></a>
                <p>{{ $loan->getDescription() }}</p>
                <strong>@lang('lend.loan.amount'): </strong> {{ $loan->getAmount() }} USD
                <strong>@lang('lend.loan.interest-rate'): </strong> {{ $loan->getInterestRate() }} %
            </li>
            <br>
        </ul>
        </div>
        </div>
        @endforeach
        {{ BootstrapHtml::paginator($paginator)->links() }}
    </div>
</div>
@stop

