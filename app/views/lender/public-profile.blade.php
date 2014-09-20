@extends('layouts.master')

@section('page-title')
{{ $lender->getUser()->getUsername() }}
@stop

@section('content')
<div class="row">
    <h1></h1>
    <div class="col-sm-4 pull-right">
        <img src="{{ $lender->getUser()->getProfilePictureUrl() }}" width="100%">
    </div>

    <div class="col-sm-8">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    {{ $lender->getUser()->getUsername() }}
                </h3>
            </div>
            <div class="panel-body">
                <p>
                    <i class="fa fa-fw fa-map-marker"></i>
                    @if($lender->getProfile()->getCity())
                        {{ $lender->getProfile()->getCity() }},&nbsp;
                    @endif
                    {{ $lender->getCountry()->getName() }}
                </p>
                <p>
                    {{ $lender->getProfile()->getAboutMe() }}
                </p>
            </div>
        </div>
    </div>
</div>
@stop
