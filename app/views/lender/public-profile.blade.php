@extends('layouts.master')

@section('page-title')
{{ $lender->getUser()->getUsername() }}
@stop

@section('content')
<div class="row lender-profile">
    <div class="col-sm-6">
        <img src="{{ $lender->getUser()->getProfilePictureUrl() }}" width="100%">
    </div>

    <div class="col-sm-6">
        <h2>{{ $lender->getUser()->getUsername() }}</h2>
        <p>
            <i class="fa fa-fw fa-map-marker"></i>
            @if($lender->getProfile()->getCity())
                {{ $lender->getProfile()->getCity() }},&nbsp;
            @endif
            {{ $lender->getCountry()->getName() }}
        </p>
        <p>
            Karma:<i class="fa fa-info-circle karma" data-toggle="tooltip"></i> {{ $karma }} 
        </p>
        <p>{{ $lender->getProfile()->getAboutMe() }}</p>
    </div>
</div>
@stop

@section('script-footer')
<script type="text/javascript">
    $('.karma').tooltip({placement: 'bottom', title: 'Karma goes up with good deeds like posting comments, inviting new lenders and helping to develop our lending group community.'})
</script>
@stop