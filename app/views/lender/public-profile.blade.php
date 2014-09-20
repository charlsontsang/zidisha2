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
        <p>{{ $lender->getProfile()->getAboutMe() }}</p>
    </div>
</div>
@stop
