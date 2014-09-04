@extends('layouts.master')

@section('page-title')
{{ $lender->getUser()->getUsername() }}
@stop

@section('content')
<div class="page-header">
    <h2>{{ $lender->getUser()->getUsername() }}</h2>
</div>
<div class="row">
    <div class="col-sm-4 pull-right mobile-padding">
        <img src="{{ $lender->getUser()->getProfilePictureUrl() }}" width="100%">
    </div>

    <div class="col-sm-6">
        <p>
            <i class="fa fa-fw fa-map-marker"></i>
            @if($lender->getProfile()->getCity())
                {{ $lender->getProfile()->getCity() }},&nbsp;
            @endif
            {{ $lender->getCountry()->getName() }}
        </p>
        <p>
            Karma:
            {{ BootstrapHtml::tooltip('lender.tooltips.profile.karma') }}
            {{ $karma }}
        </p>
        <p>{{ $lender->getProfile()->getAboutMe() }}</p>
    </div>
</div>

<div class="page-header">
    <h3><strong>Fundraising Loans</strong></h3>
</div>

@if (count($activeBids)>0)
    @foreach($activeBids as $activeBid)
    <div class="row">
        <div class="col-sm-3">
                <a class="pull-left mobile-padding" href="{{ route('loan:index', $activeBid->getLoanId()) }}">
                    <img src="{{ $activeBid->getBorrower()->getUser()->getProfilePictureUrl('small-profile-picture') }}" width="100%">
                </a>
        </div>
        <div class="col-sm-2 mobile-padding">
                {{ $activeBid->getBorrower()->getName() }}
                <br/><br/>
                {{ $activeBid->getBorrower()->getProfile()->getCity() }},
                {{ $activeBid->getBorrower()->getCountry()->getName() }}
        </div>
        <div class="col-sm-3 mobile-padding">
                <a href="{{ route('loan:index', $activeBid->getLoanId()) }}">{{ $activeBid->getLoan()->getSummary() }}</a>
        </div>
        <div class="col-sm-4 mobile-padding"> 
            @include('partials/loan-progress', [ 'loan' => $activeBid->getLoan() ]) </td>
        </div>
    </div>
    <hr/>
    @endforeach

    {{ BootstrapHtml::paginator($activeBids)->links() }}
@endif

@if (count($activeLoansBids)>0)
    <div class="page-header">
        <h3><strong>Active Loans</strong></h3>
    </div>
    @foreach($activeLoansBids as $activeLoansBid)
    <div class="row">
        <div class="col-sm-3">
                <a class="pull-left mobile-padding" href="{{ route('loan:index', $activeLoansBid->getLoanId()) }}">
                    <img src="{{ $activeLoansBid->getBorrower()->getUser()->getProfilePictureUrl('small-profile-picture') }}" width="100%">
                </a>
        </div>
        <div class="col-sm-2 mobile-padding">
                {{ $activeLoansBid->getBorrower()->getName() }}
                <br/><br/>
                {{ $activeLoansBid->getBorrower()->getProfile()->getCity() }},
                {{ $activeLoansBid->getBorrower()->getCountry()->getName() }}
        </div>
        <div class="col-sm-3 mobile-padding">
                <a href="{{ route('loan:index', $activeLoansBid->getLoanId()) }}">{{ $activeLoansBid->getLoan()->getSummary() }}</a>
        </div>
    </div>
    <hr/>
    @endforeach

    {{ BootstrapHtml::paginator($activeLoansBids, 'page2')->links() }}
@endif

@if (count($completedLoansBids)>0)
    <div class="page-header">
        <h3><strong>Completed Loans</strong></h3>
    </div>
    @foreach($completedLoansBids as $completedLoansBid)
    <div class="row">
        <div class="col-sm-3">
                <a class="pull-left mobile-padding" href="{{ route('loan:index', $completedLoansBid->getLoanId()) }}">
                    <img src="{{ $completedLoansBid->getBorrower()->getUser()->getProfilePictureUrl('small-profile-picture') }}" width="100%">
                </a>
        </div>
        <div class="col-sm-2 mobile-padding">
                {{ $completedLoansBid->getBorrower()->getName() }}
                <br/><br/>
                {{ $completedLoansBid->getBorrower()->getProfile()->getCity() }},
                {{ $completedLoansBid->getBorrower()->getCountry()->getName() }}
        </div>
        <div class="col-sm-3 mobile-padding">
                <a href="{{ route('loan:index', $completedLoansBid->getLoanId()) }}">{{ $completedLoansBid->getLoan()->getSummary() }}</a>
        </div>
    </div>
    <hr/>
    @endforeach
    {{ BootstrapHtml::paginator($completedLoansBids, 'page3')->links() }}
@endif

@stop
