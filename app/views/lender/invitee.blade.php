@extends('layouts.master')

@section('page-title')
You're invited!
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
                <img src="/assets/images/carousel/esther.jpg">
                <div class="carousel-caption">
                    <h3>{{ $carouselHeading }}</h3>
                    <p>
                        <a href="{{ route('join') }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
                    </p>
                </div>
            </div>
            <div class="item">
                <img src="/assets/images/carousel/bineta.jpg">
                <div class="carousel-caption">
                    <h3>{{ $carouselHeading }}</h3>
                    <p>
                        <a href="{{ route('join') }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
                    </p>
                </div>
            </div>
            <div class="item">
                <img src="/assets/images/carousel/mary.jpg">
                <div class="carousel-caption">
                    <h3>{{ $carouselHeading }}</h3>
                    <p>
                        <a href="{{ route('join') }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
                    </p>
                </div>
            </div>
            <div class="item">
                <img src="/assets/images/carousel/aloysius.jpg">
                <div class="carousel-caption">
                    <h3>{{ $carouselHeading }}</h3>
                    <p>
                        <a href="{{ route('join') }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
                    </p>
                </div>
            </div>
            <div class="item">
                <img src="/assets/images/carousel/pherister.jpg">
                <div class="carousel-caption">
                    <h3>{{ $carouselHeading }}</h3>
                    <p>
                        <a href="{{ route('join') }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
                    </p>
                </div>
            </div>
            <div class="item">
                <img src="/assets/images/carousel/melita.jpg">
                <div class="carousel-caption">
                    <h3>{{ $carouselHeading }}</h3>
                    <p>
                        <a href="{{ route('join') }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
                    </p>
                </div>
            </div>
            <div class="item">
                <img src="/assets/images/carousel/fatou.jpg">
                <div class="carousel-caption">
                    <h3>{{ $carouselHeading }}</h3>
                    <p>
                        <a href="{{ route('join') }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
                    </p>
                </div>
            </div>
            <div class="item">
                <img src="/assets/images/carousel/elizabeth.jpg">
                <div class="carousel-caption">
                    <h3>{{ $carouselHeading }}</h3>
                    <p>
                        <a href="{{ route('join') }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
                    </p>
                </div>
            </div>
            <div class="item">
                <img src="/assets/images/carousel/thilor.jpg">
                <div class="carousel-caption">
                    <h3>{{ $carouselHeading }}</h3>
                    <p>
                        <a href="{{ route('join') }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
                    </p>
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
<div class="row">
    <div class="col-md-8 col-md-offset-2 info-page home">
        
        @if($lender->getUser()->hasProfilePicture())
        <img class="profile-image" style="width:100px" src="{{ $lender->getUser()->getProfilePictureUrl() }}"/>
        <br/><br/>
        @endif
        <h3>
            You have received a $25 lending credit from 
            <a href="{{ route('lender:public-profile', $lender->getUser()->getUsername()) }} ">
                {{ $lender->getUser()->getUsername()}}
            </a>.
        </h3>
        <p>To redeem your credit, simply <a href="{{ route('join') }}">create a free lending account</a>.  
        You'll receive a $25 credit to fund a loan project of your choice, and you can follow the project's progress as the loan is repaid to the organization.</p>
        <p>If you enjoy helping our entrepreneurs achieve their dreams, we hope you'll return to make another loan in the future.

        &nbsp;&nbsp;&nbsp;&nbsp;
        <a href="{{ route('lender:how-it-works') }}">Learn more</a>
        <br/><br/>
        <p class="text-center">
            <a class="btn btn-primary btn-lg" href="{{ route('join') }}">{{ $buttonText }}</a>
        </p>

        <hr/>

        @include('partials.why-zidisha')

        <p class="text-center">
            <a class="btn btn-primary btn-lg" href="{{ route('join') }}">{{ $buttonText }}</a>
        </p>
    </div>
</div>
@stop
