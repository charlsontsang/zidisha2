@extends('layouts.master')

@section('page-title')
You're invited!
@stop

@section('content-top')

@include('partials.carousel')

@stop

@section('content')
    <div class="row home-section info-page home text-center">
        <div class="col-md-10 col-md-offset-1">
            
            @if($lender->getUser()->hasProfilePicture())
            <img class="profile-image" style="width:100px" src="{{ $lender->getUser()->getProfilePictureUrl() }}"/>
            @endif
            <h2 class="lead alpha">
                You have received a $25 lending credit from 
                <a href="{{ route('lender:public-profile', $lender->getUser()->getUsername()) }} ">
                    {{ $lender->getUser()->getUsername()}}
                </a>.
            </h2>
            <p class="text-large lead">Zidisha is the first online microlending community that directly connects lenders and borrowers - no matter the distance or disparity between them.  
            <p class="text-large lead">Use your $25 credit to fund a loan project of your choice, and follow the project's progress as the loan is repaid to the organization.
            &nbsp;&nbsp;&nbsp;&nbsp;
            <a href="{{ route('lender:how-it-works') }}">How invite credits work</a></p>
            <p>
                <a class="btn btn-home btn-lg" href="{{ $buttonLink }}">{{ $buttonText }}</a>
            </p>
        </div>
    </div>
</div> <!-- /container -->

@include('partials.landing-page')

<div class="container">
  <div class="row home-section info-page home text-center">

    <div class="col-md-8 col-md-offset-2">
      <h2 class="text-large lead">Enjoy connecting with remarkable people around the world and helping them reach their goals.</h2>

      <p>
          <a class="btn btn-home btn-lg btn-home-bottom" href="{{ $buttonLink }}">{{ $buttonText }}</a>
      </p>

    </div>
  </div>
</div>

@stop
