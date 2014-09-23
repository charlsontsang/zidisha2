@extends('layouts.master')

@section('page-title')
You're invited!
@stop

@section('content-top')

@include('partials.carousel')

@stop

@section('content')
</div> <!-- /container -->
<div class="home">
  <div class="container-fluid highlight">
    <div class="container">
      <div class="row home-section info-page text-center">
          <div class="col-md-10 col-md-offset-1">
              
              @if($lender->getUser()->hasProfilePicture())
              <img class="profile-image" style="width:100px" src="{{ $lender->getId() }}"/>
              @endif
              <h2 class="alpha">
                  You have received a $25 lending credit from 
                  <a href="{{ route('lender:public-profile', $lender->getId()) }} ">
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
  </div> <!-- /container-fluid -->

  @include('partials.landing-page')

  <div class="container-fluid highlight">
    <div class="container">
      <div class="row home-section info-page text-center">


        <div class="col-md-8 col-md-offset-2">
          <p class="text-large lead home-bottom">Enjoy connecting with remarkable people around the world and helping them reach their goals.</p>

          <p>
              <a class="btn btn-home btn-lg" href="{{ $buttonLink }}">{{ $buttonText }}</a>
          </p>

        </div>
      </div>
    </div>
  </div>
</home>

@stop