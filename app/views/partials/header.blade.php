<div class="navbar navbar-default navbar-static-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ route('home') }}">
                zidisha
            </a>
        </div>
        <div class="collapse navbar-collapse navbar-right">
            <ul class="nav navbar-nav">
                <li><a href="#">Lend</a></li>
                <li><a href="#">Borrow</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        Learn More <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Member Updates &amp; Photos</a></li>
                        <li><a href="{{ route('page:our-story') }}">Our Story</a></li>
                        <li><a href="#">Why Zidisha?</a></li>
                        <li><a href="{{ route('page:how-it-works') }}">How It Works</a></li>
                        <li><a href="#">Trust &amp; Security</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Team</a></li>
                        <li><a href="#">Statistics</a></li>
                        <li><a href="#">Press</a></li>
                    </ul>
                </li>
            </ul>
            <form class="navbar-form navbar-left">
                <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#LoginModal">Log In</a>
            </form>
        </div>
    </div>
</div>
