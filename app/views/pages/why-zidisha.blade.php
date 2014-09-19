@extends('layouts.master')

@section('page-title')
Why Zidisha?
@stop


@section('content')
<div class="row">
        <div class="col-sm-4">
            <ul class="nav side-menu" role="complementary">
              <h4>About</h4>
              @include('partials.nav-links.about-links')
            </ul>
        </div>

        <div class="col-sm-8 info-page highlight highlight-panel">
        <div class="page-header">
            <h1>Why Zidisha?</h1>
        </div>
        
        <p>Zidisha is the first online microlending community that directly connects lenders and borrowers — no matter the distance or disparity between them. We bypass expensive local banks and intermediaries that charge sky-high interest rates and offer a person-to-person platform that lets lenders and borrowers communicate openly and instantly.</p>

        <h3>More than charity</h3>
        <p>Developing countries are home to unbelievable amounts of energy, ingenuity, and ambition. And because millions of young adults in developing countries are now online, they no longer need charities or aid organizations to tell their remarkable stories.</p>
        <p>But while they <i>may</i> have access to online social connectivity, they <i>don’t</i> have access to the resources needed to live up to all that ambition. Formal jobs are scarce. Self-employed workers make meager earnings and lack the capital needed to grow their businesses. Local banks rarely help them out.</p>
        <p>Learn <a href="http://p2p-microlending-blog.zidisha.org/2014/04/13/spotlight-on-theresia-kabiti-kenya/">how an inspiring schoolteacher in Kenya teamed up with Zidisha lenders</a> to bring education opportunities to dozens of children.</p>

        <img src="{{ asset('assets/images/pages/why-zidisha/theresia.jpg'); }}" width="100%" />

        <h3>No middleman</h3>
        <p>We eliminate the middleman so that you can more fully unleash someone’s business potential. Unlike more mainstream microloan websites that use local intermediary organizations, Zidisha offers a direct person-to-person lending platform that eliminates intermediaries completely. Check out <a href="http://p2p-microlending-blog.zidisha.org/2014/03/15/spotlight-on-siaka-traore-burkina-faso/">how a farmer in a remote village in Burkina Faso used Zidisha to bypass restrictive local institutions</a> and access business growth loans for the first time in his life.</p>

        <h3>Lower cost for borrowers</h3>
        <p>Why is direct better? Because loans managed by local intermediary organizations charge exorbitant interest rates to the borrowers to cover their own administrative costs.  Lower cost means borrowers keep more of their earnings.</p>
        <p>Learn <a href="http://p2p-microlending-blog.zidisha.org/2014/03/22/spotlight-on-ndeye-bineta-sarr-senegal/">how Zidisha loans helped a seamstress in Senegal quadruple her dress production</a> - and how she used the profits to send her son to college.</p>
        
        <img src="{{ asset('assets/images/pages/why-zidisha/bineta.jpg'); }}" width="100%" />

        <h3>Lenders choose the interest</h3>
        <p>Not only do we eliminate the middleman to dramatically reduce the cost of microlending for borrowers, we also give lenders the opportunity to make interest on the loans they fund. It’s a win-win: Zidisha borrowers pay far less and lenders have the right to set the interest rate, if any, at which to lend their funds.  <a href="{{ route('page:how-it-works') }}">Learn how it works.</a></p>

        <h3>Direct dialogue with borrowers</h3>
        <p>Did we mention that lenders and borrowers can communicate with each other directly? That’s right — you can see everyone’s success stories unfold right before your eyes and experience it with them from the other side of the world.  <a href="#">Check out some of the dialogues going on right now.</a></p>
        
        <img src="{{ asset('assets/images/pages/why-zidisha/aloysius.jpg'); }}" width="100%" />

        <h3>Some helpful reads</h3>
        <p>Many microlending sites look similar to Zidisha, and it can be hard to choose.  Here are some pages we put together to help you learn more.</p>
        <p>
          <ul>
              <li><a href="{{ route('page:our-story') }}">Read about Zidisha's founding.</a></li>
              <li><a href="{{ route('page:team') }}">Meet our amazing volunteer team.</a></li>
              <li><a href="http://p2p-microlending-blog.zidisha.org/">Explore the remarkable loan stories in our blog.</a></li>
              <li><a href="{{ route('page:press') }}">See what the press has to say about Zidisha.</a></li>
              <li><a href="{{ route('page:faq') }}">Browse our Frequently Asked Questions.</a></li>
          </ul>
        </p>
    </div>
</div>
@stop