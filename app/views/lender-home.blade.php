@extends('layouts.master')

@section('page-title')
    Person-to-person microlending
@stop

@section('content-top')

@include('partials.carousel')

@stop

@section('content')
  <div class="row home-section info-page home text-center">
    <div class="col-md-10 col-md-offset-1">
      <h2 class="alpha">Direct loans that make dramatic impact</h2>

      <p class="text-large lead">Zidisha is the first online microlending community that directly connects lenders and borrowers — no matter the distance or disparity between them.</p>
      <p class="text-large lead"><a href="{{ route('page:statistics') }}"><strong>More than 15,000 people worldwide</strong></a> have started using Zidisha.</p>
    
    </div>
  </div>
</div> <!-- /container -->

@include('partials.landing-page')

<div class="container">
  <div class="row home-section info-page home text-center">

    <div class="col-md-8 col-md-offset-2">
      <p class="text-large lead">Enjoy connecting with remarkable people around the world and helping them reach their goals.</p>

      <p>
          <a class="btn btn-home btn-lg btn-home-bottom" href="{{ $buttonLink }}">{{ $buttonTextBottom }}</a>
      </p>

    </div>
  </div>
</div>

@stop
