@extends('layouts.side-menu-simple')

@section('page-title')
Statistics
@stop

@section('menu-title')
About
@stop

@section('menu-links')
@include('partials.nav-links.about-links')
@stop

@section('page-content')
<div class="panel panel-info info-page">
    <div class="panel-heading">
        <h3 class="panel-title">
            Community Statistics
        </h3>
    </div>
    <div class="panel-body">
        <p><em>What the whole Zidisha community has achieved so far</em></p>

        <p>
            Loan money raised:{{ BootstrapHtml::tooltip('lender.tooltips.pages.loan-money-raised') }}
            <strong>${{ number_format($totalStatistics['disbursed_amount'], 0, ".", ","); }}</strong>
        </p>
        <p>
            Loan projects funded:{{ BootstrapHtml::tooltip('lender.tooltips.pages.loan-projects-funded') }}
            <strong>{{ number_format($totalStatistics['raised_count'], 0, ".", ","); }}</strong>
        </p>
        <p>
            Lenders joined: 
            <strong>{{ number_format($totalStatistics['lenders_count'], 0, ".", ","); }}</strong>
        </p>
        <p>
            Borrowers joined:
            <strong>{{ number_format($totalStatistics['borrowers_count'], 0, ".", ",") }}</strong>
        </p>
        <p>
            Total members:
            <strong>{{ number_format($totalStatistics['lenders_count'] + $totalStatistics['borrowers_count'], 0, ".", ",") }}</strong>
        </p>
        <p>
            Countries represented by Zidisha members:
            <strong>{{ number_format($totalStatistics['countries_count'], 0, ".", ",") }}</strong>
        </p>
    </div>
</div>
<div class="panel panel-info info-page">
    <div class="panel-heading">
        <h3 class="panel-title">
            Lending Statistics
        </h3>
    </div>
    <div class="panel-body">
        <p><em>Use the dropdowns below to get filtered performance statistics for all loans funded since our founding in 2009.</em></p>
        <span style="font-size: 18px; font-weight: 300;">Display data for loans disbursed within:&nbsp;&nbsp;&nbsp;
            <div class="btn btn-default btn-filter" target="#filter-time-periods">
                {{ $selectedTimePeriod ? array_get($timePeriods, $selectedTimePeriod) : 'All time' }}
               <i class="fa fa-fw fa-caret-down"></i>
            </div>
        </span>
        <br/><br/>
        <span style="font-size: 18px; font-weight: 300;">Display data for loans in:&nbsp;&nbsp;&nbsp;
            <div class="btn btn-default btn-filter" target="#filter-countries">
                {{ $selectedCountry ? $selectedCountry->getName() : 'All Countries' }}
               <i class="fa fa-fw fa-caret-down"></i>
            </div>
        </span>
        <br/><br/>
        <p>
            Loan money raised:{{ BootstrapHtml::tooltip('lender.tooltips.pages.loan-money-raised-filtered') }}
            <strong>${{ number_format($lendingStatistics['disbursed_amount'], 0, ".", ","); }}</strong>
        </p>
        <p>
            Loan projects funded:{{ BootstrapHtml::tooltip('lender.tooltips.pages.loan-projects-funded-filtered') }}
            <strong>{{ number_format($lendingStatistics['raised_count'], 0, ".", ","); }}</strong>
        </p>
        <p>
            Average lender interest:{{ BootstrapHtml::tooltip('lender.tooltips.pages.average-lender-interest') }}
            <strong>{{ number_format($lendingStatistics['average_lender_interest'], 1, ".", ","); }}%</strong>
        </p>
        @if ($lendingStatistics['repaid_amount'])
            <p>
                Principal repaid:{{ BootstrapHtml::tooltip('lender.tooltips.pages.principal-repaid') }}
                <strong>${{ number_format($lendingStatistics['repaid_amount'], 0, ".", ","); }}
                ({{ number_format($lendingStatistics['repaid_rate'], 1, ".", ","); }}% of amount disbursed)</strong>
            </p>
        @endif
        @if ($lendingStatistics['outstanding_on_time_amount'])
            <p>
                Principal held by borrowers repaying on time (within 30-day threshold):{{ BootstrapHtml::tooltip('lender.tooltips.pages.principal-repaid-on-time') }}
                <strong>${{ number_format($lendingStatistics['outstanding_on_time_amount'], 0, ".", ",") }}
                ({{ number_format($lendingStatistics['outstanding_on_time_rate'], 1, ".", ","); }}% of amount disbursed)</strong>
            </p>
        @endif
        @if ($lendingStatistics['outstanding_late_amount'])
            <p>
                Principal held by borrowers more than 30 days past due with scheduled repayments:{{ BootstrapHtml::tooltip('lender.tooltips.pages.principal-repaid-due') }}
                <strong>${{ number_format($lendingStatistics['outstanding_late_amount'], 0, ".", ",") }}
                ({{ number_format($lendingStatistics['outstanding_late_rate'], 1, ".", ","); }}% of amount disbursed)</strong>
            </p>
        @endif
        @if ($lendingStatistics['forgiven_amount'])
             <p>
                Principal that has been forgiven by lenders:{{ BootstrapHtml::tooltip('lender.tooltips.pages.principal-forgiven') }}
                <strong>${{ number_format($lendingStatistics['forgiven_amount'], 0, ".", ","); }}
                ({{ number_format($lendingStatistics['forgiven_rate'], 1, ".", ","); }}% of amount disbursed)</strong>
             </p>
        @endif
        @if ($lendingStatistics['written_off_amount'])
            <p>
                Principal that has been written off:{{ BootstrapHtml::tooltip('lender.tooltips.pages.principal-written-off') }}
                <strong>${{ number_format($lendingStatistics['written_off_amount'], 0, ".", ","); }}
                ({{ number_format($lendingStatistics['written_off_rate'], 1, ".", ","); }}% of amount disbursed)</strong>
        @endif
        <!-- TO DO 
        <p>Want to dive deeper? You can see the individual loan reports that provided the raw data for these statistics <a href="https://www.zidisha.org/index.php?p=114">here</a>.</p>
        -->
    </div>
</div>

<div id="filter-countries" class="hide">
    <ul class="list-unstyled">
        @if($selectedCountry == null)
        <strong>All Countries</strong>
        @else
        <li>
            <a href="{{ route('page:statistics', ['country' => 'everywhere'] + $routeParams) }}"> All Countries </a>
        </li>
        @endif
        @foreach($countries as $country)
        <li>
            @if($selectedCountry == $country)
            <strong>{{ $country->getName()}}</strong>
            @else
                <a href="{{ route('page:statistics', ['country' => $country->getSlug()] + $routeParams) }}">
                {{ $country->getName()}} </a>
            @endif
        </li>
        @endforeach
    </ul>
</div>


<div id="filter-time-periods" class="hide">
    <ul class="list-unstyled">
        @foreach($timePeriods as $key=>$timePeriod)
        <li>
            @if($selectedTimePeriod == $timePeriod)
            <strong>{{ $timePeriod }}</strong>
            @else
            <a href="{{ route('page:statistics', ['timePeriod' => $key] + $routeParams) }}"> {{
                $timePeriod }} </a>
            @endif
        </li>
        @endforeach
    </ul>
</div>
@stop
