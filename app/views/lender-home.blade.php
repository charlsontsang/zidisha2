@extends('layouts.master')

@section('page-title')
    Person-to-person microlending
@stop

@section('content-top')
    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" data-interval="0">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
            <li data-target="#carousel-example-generic" data-slide-to="1"></li>
            <li data-target="#carousel-example-generic" data-slide-to="1"></li>
            <li data-target="#carousel-example-generic" data-slide-to="1"></li>
            <li data-target="#carousel-example-generic" data-slide-to="1"></li>
            <li data-target="#carousel-example-generic" data-slide-to="1"></li>
        </ol>
    
        <!-- Wrapper for slides -->
        <div class="carousel-inner" id="esther">
            <div class="item active">
                <img src="assets/images/carousel/esther.jpg">
                <div class="carousel-caption caption-left">
                    <h3>Lend <span class="text-primary">Esther</span> $50 to open a grocery shop</h3>
                    <p>{{ $secondaryCaption }}</p>
                    <a href="{{ route('lend:index') }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
                </div>
                <div class="carousel-gradient-left"></div>
            </div>
            <div class="item">
                <img src="assets/images/carousel/bineta.jpg">
                <div class="carousel-caption caption-right">
                    <h3>Lend <span class="text-primary">Bineta</span> $60 for a sewing machine</h3>
                    <p>{{ $secondaryCaption }}</p>
                    <a href="{{ route('lend:index') }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
                </div>
                <div class="carousel-gradient-right"></div>
            </div>
            <div class="item">
                <img src="assets/images/carousel/mary.jpg">
                <div class="carousel-caption">
                    <h3>Lend <span class="text-primary">Mary</span> $50 for a delivery wagon</h3>
                    <p>{{ $secondaryCaption }}</p>
                    <a href="{{ route('lend:index') }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
                </div>
            </div>
            <div class="item">
                <img src="assets/images/carousel/melita.jpg">
                <div class="carousel-caption">
                    <h3>Lend <span class="text-primary">Melita</span> $100 for a dairy cow</h3>
                    <p>{{ $secondaryCaption }}</p>
                    <a href="{{ route('lend:index') }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
                </div>
            </div>
            <div class="item">
                <img src="assets/images/carousel/fatou.jpg">
                <div class="carousel-caption">
                    <h3>Lend <span class="text-primary">Fatou</span> $100 to open a beauty salon</h3>
                    <p>{{ $secondaryCaption }}</p>
                    <a href="{{ route('lend:index') }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
                </div>
            </div>
            <div class="item">
                <img src="assets/images/carousel/elizabeth.jpg">
                <div class="carousel-caption">
                    <h3>Lend <span class="text-primary">Elizabeth</span> $75 for a mobile phone shop</h3>
                    <p>{{ $secondaryCaption }}</p>
                    <a href="{{ route('lend:index') }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
                </div>
            </div>
        </div>
    
        <!-- Controls -->
        <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left"></span>
        </a>
        <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right"></span>
        </a>
    </div>
@stop

@section('content')

@include('partials.landing-page')

<div class="container">
  <div class="row home-section info-page home text-center">

    <div class="col-md-8 col-md-offset-2">
      <h2 class="lead">Enjoy connecting with remarkable people around the world and helping them reach their goals.</h2>

      <p>
          <a class="btn btn-home btn-lg" href="{{ route('lend:index') }}">{{ $buttonTextBottom }}</a>
      </p>

    </div>
  </div>
</div>

@stop
