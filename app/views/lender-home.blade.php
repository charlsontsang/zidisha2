@extends('layouts.master')

@section('content-top')
    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" data-interval="0">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
            <li data-target="#carousel-example-generic" data-slide-to="1"></li>
        </ol>
    
        <!-- Wrapper for slides -->
        <div class="carousel-inner">
            <div class="item active" style="height: 600px">
                <img src="assets/images/flickr/bineta.jpg" alt="...">
                <div class="carousel-caption">
                    <h3>Lend <span class="text-primary">Bineta</span> $60 for a sewing machine</h3>
                    <p>and join the global <strong>person-to-person</strong> microlending movement.</p>
                    <p>
                        <a href="#" class="btn btn-primary btn-lg">View Our Entrepreneurs</a>
                    </p>
                </div>
            </div>
            <div class="item" style="height: 600px">
                <img src="assets/images/flickr/mary.jpg" alt="...">
                <div class="carousel-caption">
                    <h3>Lend <span class="text-primary">Mary</span> $50 for a delivery wagon</h3>
                    <p>and join the global <strong>person-to-person</strong> microlending movement.</p>
                    <p>
                        <a href="#" class="btn btn-primary btn-lg">View Our Entrepreneurs</a>
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
    <div class="col-md-10 col-md-offset-1">
        <br/>
        <h3 class="text-center">
            P2P LENDING ACROSS THE INTERNATIONAL WEALTH DIVIDE.
        </h3>
        <br/>
        <p>
            We are pioneering the first online microlending community to connect lenders and borrowers
            directly across international borders - overcoming previously insurmountable barriers of geography,
            wealth and circumstance.
        </p>

        <p>
            People in developing countries support their families with their own small businesses.
            They need loans in order to grow - but local banks charge exorbitant interest rates.            
        </p>

        <p>
            We bypass expensive local banks and connect lenders and borrowers directly.
            The result is a fairly priced loan - and a friendship that transcends geography.
        </p>
    </div>
</div>

<hr/>
@include('partials.how-it-works-steps')
<hr/>

<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <h3 class="text-center">
            BECOME A LENDER
        </h3>
        <br/>
        <p>
            Join us and start making a difference.
            You can explore entrepreneur stories, find a loan project to support,
            and connect with others who share the vision of a world where responsible
            and motivated people have the opportunity to pursue their goals regardless of their location.
        </p>
        <br/>
        <p class="text-center">
            <a class="btn btn-primary btn-lg" href="#">VIEW ENTREPRENEURS</a>
        </p>
    </div>
</div>
@stop
