@extends('layouts.master')

@section('page-title')
Lend
@stop

@section('content')
<div class="page-header">
    <h1>Lend</h1>
</div>


<div class="row">
    <div class="col-xs-4">
        <h2>Categories</h2>

        <ul class="list-unstyled">
            <li>
                <a href="{{ route('lend:index') }}"> All </a>
            </li>
            @foreach($loanCategories as $loanCategory)
            <li>
                @if($selectedLoanCategory == $loanCategory)
                <strong>{{ $loanCategory->getName()}}</strong>
                @else
                <a href="{{ route('lend:index') }}?loan_category_id={{ $loanCategory->getId() }}"> {{
                    $loanCategory->getName()}} </a>
                @endif
            </li>
            @endforeach
        </ul>

        <h2>Countries</h2>

        <ul class="list-unstyled">
            <li>
                <a href="{{ route('lend:index') }}"> EveryWhere </a>
            </li>
            @foreach($countries as $country)
            <li>
                @if($selectedCountry == $country)
                <strong>{{ $country->getName()}}</strong>
                @else
                <a href="{{ route('lend:index') }}?country_id={{ $country->getId() }}"> {{ $country->getName()}} </a>
                @endif
            </li>
            @endforeach
        </ul>
    </div>

    <div class="col-xs-8">
        @if($selectedLoanCategory)
        <h2>{{ $selectedLoanCategory->getName(); }}</h2>
        <br>

        <p><strong>How it works: </strong> {{ $selectedLoanCategory->getHowDescription() }} </p> <br>

        <p><strong>Why it's important: </strong> {{ $selectedLoanCategory->getWhyDescription() }} </p> <br>

        <p><strong>What your loan can do: </strong> {{ $selectedLoanCategory->getWhatDescription() }} </p>
        @endif


        @if($selectedCountry)
        <h2>{{ $selectedCountry->getName(); }}</h2>
        <br>
        @endif
    </div>
</div>
@stop

