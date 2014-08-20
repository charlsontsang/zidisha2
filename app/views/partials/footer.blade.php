<div class="page-section page-section-inverted footer">
    <br/>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div>
                    <div>
                        <a href="{{ route('home') }}">
                            <span class="sr-only">Zidisha</span>
                            <img src="{{ '/assets/images/logo-small-footer.png' }}" alt="Zidisha Logo"/>
                        </a>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="dropdown">
                    <br/>
                    <a data-toggle="dropdown" href="#">
                        English
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <li role="presentation">
                            <a role="menuitem" tabindex="-1" href="#">Français</a>
                            <a role="menuitem" tabindex="-1" href="#">Bahasa Indonesia</a>
                        </li>
                    </ul>
                </div>
                    <div>
                        <p>
                            <br/>
                            Join us at 
                        </p>
                        <ul class="list-unstyled list-inline">
                            <li>
                                <a href="http://p2p-microlending-blog.zidisha.org/" target="_blank">
                                    <span class="fa fa-rss-square fa-2x"></span>
                                    <span class="sr-only">Blog</span>
                                </a>
                            </li>
                            <li>
                                <a href="http://www.facebook.com/ZidishaInc?sk=wall" target="_blank">
                                    <span class="fa fa-facebook-square fa-2x"></span>
                                    <span class="sr-only">Facebook</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://twitter.com/ZidishaInc" target="_blank">
                                    <span class="fa fa-twitter-square fa-2x"></span>
                                    <span class="sr-only">Twitter</span>
                                </a>
                            </li>
                        </ul>
                    </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-xs-4">
                        <div class="h3">Explore</div>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('page:our-story') }}">Our Story</a></li>
                            <li><a href="{{ route('page:how-it-works') }}">How It Works</a></li>
                            <li><a href="{{ route('page:trust-and-security') }}">Trust & Security</a></li>
                            <li><a href="{{ route('page:faq') }}">FAQ</a></li>
                            <li><a href="{{ route('page:team') }}">Team</a></li>
                            <li><a href="#">Statistics</a></li>
                            <li><a href="{{ route('page:press') }}">Press</a></li>
                        </ul>
                    </div>
                    <div class="col-xs-4">
                        <div class="h3">Act</div>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('lend:index') }}">Lend</a></li>
                            <li><a href="{{ route('page:volunteer') }}">Volunteer</a></li>
                            <li><a href="{{ route('lender:gift-cards') }}">Gift Cards</a></li>
                            <li><a href="#">Donate</a></li>
                            <li><a href="http://www.amazon.com/Venture-Collection-True-Microfinance-Stories-ebook/dp/B009JC6V12">Book</a></li>
                        </ul>
                    </div>
                    <div class="col-xs-4">
                        <div class="h3">Connect</div>
                        <ul class="list-unstyled">
                            <li><a href="#">Project Updates</a></li>
                            <li><a href="http://p2p-microlending-blog.zidisha.org/">Blog</a></li>
                            <li><a href="http://www.facebook.com/ZidishaInc?sk=wall">Facebook</a></li>
                            <li><a href="https://twitter.com/ZidishaInc">Twitter</a></li>
                            <li><a href="{{ route('lender:groups') }}">Lending Groups</a></li>
                            <li><a href="https://www.zidisha.org/forum/">Forum</a></li>
                        </ul>
                    </div>
                </div>  
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-9">
                <p>&copy; {{ date('Y') }} Zidisha, Inc.&nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp;Zidisha is a 501(c)(3) nonprofit&nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp;<a href="{{ route('page:terms-of-use') }}">Terms and Privacy</a>&nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp;<a href="{{ route('page:contact') }}">Contact Us</a></p>
            </div>
            <div class="col-md-3">
                <ul class="list-unstyled list-inline pull-right">
                    <li>
                        <a href="https://mixpanel.com/f/partner"><img src="//cdn.mxpnl.com/site_media/images/partner/badge_light.png" alt="Mobile Analytics" /></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
