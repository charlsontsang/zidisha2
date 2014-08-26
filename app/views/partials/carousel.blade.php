<div id="carousel-example-generic" class="carousel slide" data-ride="carousel" data-interval="0">
    <!-- Indicators -->
    <ol class="carousel-indicators">
        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner" id="esther">
        <div class="item active">
            <img src="/assets/images/carousel/esther.jpg">
            <div class="carousel-caption caption-left">
                <h3>
                    @if (!empty($estherCaption))
                        {{ $estherCaption }}
                    @else
                        {{ $inviteeCaption }}
                    @endif
                </h3>
                @if (!empty($secondaryCaption))
                    <p>{{ $secondaryCaption }}</p>
                @endif
                <a href="{{ $buttonLink }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
            </div>
            <div class="carousel-gradient-left"></div>
        </div>
        <div class="item">
            <img src="/assets/images/carousel/fatou.jpg">
            <div class="carousel-caption caption-left">
                <h3>
                    @if (!empty($fatouCaption))
                        {{ $fatouCaption }}
                    @else
                        {{ $inviteeCaption }}
                    @endif
                </h3>
                @if (!empty($secondaryCaption))
                    <p>{{ $secondaryCaption }}</p>
                @endif
                <a href="{{ $buttonLink }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
            </div>
            <div class="carousel-gradient-left"></div>
        </div>
        <div class="item">
            <img src="/assets/images/carousel/melita.jpg">
            <div class="carousel-caption caption-right">
                <h3>
                    @if (!empty($melitaCaption))
                        {{ $melitaCaption }}
                    @else
                        {{ $inviteeCaption }}
                    @endif
                </h3>
                @if (!empty($secondaryCaption))
                    <p>{{ $secondaryCaption }}</p>
                @endif
                <a href="{{ $buttonLink }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
            </div>
            <div class="carousel-gradient-right"></div>
        </div>
        <div class="item">
            <img src="/assets/images/carousel/bineta.jpg">
            <div class="carousel-caption caption-right">
                <h3>
                    @if (!empty($binetaCaption))
                        {{ $binetaCaption }}
                    @else
                        {{ $inviteeCaption }}
                    @endif
                </h3>
                @if (!empty($secondaryCaption))
                    <p>{{ $secondaryCaption }}</p>
                @endif
                <a href="{{ $buttonLink }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
            </div>
            <div class="carousel-gradient-right"></div>
        </div>
        <div class="item">
            <img src="/assets/images/carousel/mary.jpg">
            <div class="carousel-caption caption-right">
                <h3>
                    @if (!empty($maryCaption))
                        {{ $maryCaption }}
                    @else
                        {{ $inviteeCaption }}
                    @endif
                </h3>
                @if (!empty($secondaryCaption))
                    <p>{{ $secondaryCaption }}</p>
                @endif
                <a href="{{ $buttonLink }}" class="btn btn-primary btn-lg">{{ $buttonText }}</a>
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