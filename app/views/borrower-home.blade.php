@extends('layouts.master')

@section('page-title')
    {{ \Lang::get('borrower.borrower-home.title') }}
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
                    <h3>{{ \Lang::get('borrower.borrower-home.carousel-heading') }}</h3>
                    <p>{{ \Lang::get('borrower.borrower-home.secondary-caption') }}</p>
                    <p>
                        <a href="#" class="btn btn-primary btn-lg">{{ \Lang::get('borrower.borrower-home.button-text') }}</a>
                    </p>
                </div>
            </div>
            <div class="item">
                <img src="assets/images/carousel/bineta.jpg">
                <div class="carousel-caption">
                    <h3>{{ \Lang::get('borrower.borrower-home.carousel-heading') }}</h3>
                    <p>{{ \Lang::get('borrower.borrower-home.secondary-caption') }}</p>
                    <p>
                        <a href="#" class="btn btn-primary btn-lg">{{ \Lang::get('borrower.borrower-home.button-text') }}</a>
                    </p>
                </div>
            </div>
            <div class="item">
                <img src="assets/images/carousel/mary.jpg">
                <div class="carousel-caption">
                    <h3>{{ \Lang::get('borrower.borrower-home.carousel-heading') }}</h3>
                    <p>{{ \Lang::get('borrower.borrower-home.secondary-caption') }}</p>
                    <p>
                        <a href="#" class="btn btn-primary btn-lg">{{ \Lang::get('borrower.borrower-home.button-text') }}</a>
                    </p>
                </div>
            </div>
            <div class="item">
                <img src="assets/images/carousel/aloysius.jpg">
                <div class="carousel-caption">
                    <h3>{{ \Lang::get('borrower.borrower-home.carousel-heading') }}</h3>
                    <p>{{ \Lang::get('borrower.borrower-home.secondary-caption') }}</p>
                    <p>
                        <a href="#" class="btn btn-primary btn-lg">{{ \Lang::get('borrower.borrower-home.button-text') }}</a>
                    </p>
                </div>
            </div>
            <div class="item">
                <img src="assets/images/carousel/pherister.jpg">
                <div class="carousel-caption">
                    <h3>{{ \Lang::get('borrower.borrower-home.carousel-heading') }}</h3>
                    <p>{{ \Lang::get('borrower.borrower-home.secondary-caption') }}</p>
                    <p>
                        <a href="#" class="btn btn-primary btn-lg">{{ \Lang::get('borrower.borrower-home.button-text') }}</a>
                    </p>
                </div>
            </div>
            <div class="item">
                <img src="assets/images/carousel/melita.jpg">
                <div class="carousel-caption">
                    <h3>{{ \Lang::get('borrower.borrower-home.carousel-heading') }}</h3>
                    <p>{{ \Lang::get('borrower.borrower-home.secondary-caption') }}</p>
                    <p>
                        <a href="#" class="btn btn-primary btn-lg">{{ \Lang::get('borrower.borrower-home.button-text') }}</a>
                    </p>
                </div>
            </div>
            <div class="item">
                <img src="assets/images/carousel/fatou.jpg">
                <div class="carousel-caption">
                    <h3>{{ \Lang::get('borrower.borrower-home.carousel-heading') }}</h3>
                    <p>{{ \Lang::get('borrower.borrower-home.secondary-caption') }}</p>
                    <p>
                        <a href="#" class="btn btn-primary btn-lg">{{ \Lang::get('borrower.borrower-home.button-text') }}</a>
                    </p>
                </div>
            </div>
            <div class="item">
                <img src="assets/images/carousel/elizabeth.jpg">
                <div class="carousel-caption">
                    <h3>{{ \Lang::get('borrower.borrower-home.carousel-heading') }}</h3>
                    <p>{{ \Lang::get('borrower.borrower-home.secondary-caption') }}</p>
                    <p>
                        <a href="#" class="btn btn-primary btn-lg">{{ \Lang::get('borrower.borrower-home.button-text') }}</a>
                    </p>
                </div>
            </div>
            <div class="item">
                <img src="assets/images/carousel/thilor.jpg">
                <div class="carousel-caption">
                    <h3>{{ \Lang::get('borrower.borrower-home.carousel-heading') }}</h3>
                    <p>{{ \Lang::get('borrower.borrower-home.secondary-caption') }}</p>
                    <p>
                        <a href="#" class="btn btn-primary btn-lg">{{ \Lang::get('borrower.borrower-home.button-text') }}</a>
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
        
        <h3>{{ \Lang::get('borrower.borrower-home.heading1') }}</h3>

        <p>{{ \Lang::get('borrower.borrower-home.paragraph1') }}</p>

        <h3>{{ \Lang::get('borrower.borrower-home.heading2') }}</h3>

        <p>{{ \Lang::get('borrower.borrower-home.paragraph2a') }}</p>
        <p>{{ \Lang::get('borrower.borrower-home.paragraph2b') }}</p>

        <h3>{{ \Lang::get('borrower.borrower-home.heading3') }}</h3>

        <p>{{ \Lang::get('borrower.borrower-home.paragraph3') }}</p>

        <br/><br/>
        
        <p class="text-center">
            <a class="btn btn-primary btn-lg" href="#">{{ \Lang::get('borrower.borrower-home.button-text') }}</a>
        </p>
    </div>
</div>
@stop