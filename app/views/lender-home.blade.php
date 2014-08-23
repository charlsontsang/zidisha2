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
        </ol>
    
        <!-- Wrapper for slides -->
        <div class="carousel-inner">
            <div class="item active">
                <img src="assets/images/carousel/esther.jpg">
                <div class="carousel-caption">
                    <h3>Lend <span class="text-primary">Esther</span> $50 to open a grocery shop</h3>
                    <p>{{ $secondaryCaption }}</p>
                    <a href="{{ route('lend:index') }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
                </div>
            </div>
            <div class="item">
                <img src="assets/images/carousel/bineta.jpg">
                <div class="carousel-caption">
                    <h3>Lend <span class="text-primary">Bineta</span> $60 for a sewing machine</h3>
                    <p>{{ $secondaryCaption }}</p>
                    <a href="{{ route('lend:index') }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
                </div>
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
                <img src="assets/images/carousel/aloysius.jpg">
                <div class="carousel-caption">
                    <h3>Lend <span class="text-primary">Aloysius</span> $50 to make batik fabric</h3>
                    <p>{{ $secondaryCaption }}</p>
                    <a href="{{ route('lend:index') }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
                </div>
            </div>
            <div class="item">
                <img src="assets/images/carousel/pherister.jpg">
                <div class="carousel-caption">
                    <h3>Lend <span class="text-primary">Pherister</span> $40 for schoolbooks</h3>
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
            <div class="item">
                <img src="assets/images/carousel/thilor.jpg">
                <div class="carousel-caption">
                    <h3>Lend <span class="text-primary">Thilor</span> $60 to make dresses</h3>
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

<p class="text-center">
    <a class="btn btn-default btn-lg btn-home" href="{{ route('lend:index') }}">{{ $buttonTextBottom }}</a>
</p>

@stop
