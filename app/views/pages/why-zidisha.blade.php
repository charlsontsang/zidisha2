@extends('layouts.master')

@section('page-title')
Why Zidisha?
@stop


@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2 info-page">
        <div class="page-header">
            <h1>Why Zidisha?</h1>
        </div>
        <h3>Direct loans that make dramatic impact.</h3>

        <p>Zidisha is the first online microlending community that directly  connects lenders and borrowers — no matter the distance or disparity between them. We bypass expensive local banks and intermediaries that charge sky-high interest rates and offer a person-to-person platform that lets lenders and borrowers communicate openly and instantly.  <a href="/index.php?p=43">More than 14,000 people worldwide</a> have started using Zidisha.</p>
        <br/>

        <h3>No middleman.</h3>
        <p>We eliminate the middleman so that you can more fully unleash someone’s business potential. Unlike more mainstream microloan websites that use local intermediary organizations, Zidisha offers a direct person-to-person lending platform that eliminates intermediaries completely. Check out <a href="http://p2p-microlending-blog.zidisha.org/2014/03/15/spotlight-on-siaka-traore-burkina-faso/">how a farmer in a remote village in Burkina Faso used Zidisha to bypass restrictive local institutions</a> and access business growth loans for the first time in his life.</p>
        <br/>

        <blockquote>
            <p>"Because there is no intermediary in charge of the loan, the cost of borrowing is much less than with other microfinance sites.</p>
            <footer>
                <cite><a href="http://www.entrepreneur.com/article/223391" target="_blank">Entrepreneur Magazine</a></cite>
            </footer>
        </blockquote>
        <br/>

        <h3>Lower cost for borrowers.</h3>
        <p>Why is direct better? Because loans managed by local intermediary organizations charge exorbitant interest rates to the borrowers to cover their own administrative costs.  Lower cost means borrowers keep more of their earnings.  Learn <a href="http://p2p-microlending-blog.zidisha.org/2014/03/22/spotlight-on-ndeye-bineta-sarr-senegal/">how Zidisha loans helped a seamstress in Senegal quadruple her dress production</a> - and how she used the profits to send her son to college.</p>
        <br/>
        <h3>Lenders choose the interest.</h3>
        <p>Not only do we eliminate the middleman to dramatically reduce the cost of microlending for borrowers, we also give lenders the opportunity to make interest on the loans they fund. It’s a win-win: Zidisha borrowers pay far less and lenders have the right to set the interest rate, if any, at which to lend their funds.  <a href="{{ route('page:how-it-works') }}">Learn how it works.</a></p>
        <br/>

        <blockquote>
            <p>Just as Airbnb connects travelers directly to hosts, Zidisha connects lenders directly to borrowers, providing not only an affordable loan, but also a personal connection.</p>
            <footer>
                <cite><a href="http://paulbuchheit.blogspot.com/2014/03/help-me-distribute-100000-to-new.html" target="_blank">Gmail Creator Paul Buchheit</a></cite>
            </footer>
        </blockquote>  
        <br/>

        <h3>Direct dialogue with borrowers.</h3>
        <p>Did we mention that lenders and borrowers can communicate with each other directly? That’s right — you can see everyone’s success stories unfold right before your eyes and experience it with them from the other side of the world.  <a href="#">Check out some of the dialogues going on right now.</a></p>
        <br/>

        <blockquote>
            <p>By enabling the efficient flow of capital across international boundaries and wealth divisions, and by allowing lenders to connect with and send money directly to borrowers, peer-to-peer micro-lending has the ability to have an enormous impact — on a global scale.</p>
            <footer>
                <cite><a href="http://techcrunch.com/2014/01/23/zidisha-launches-a-kickstarter-style-micro-lending-platform-for-low-income-entrepreneurs-in-developing-countries/" target="_blank">TechCrunch</a></cite>
            </footer>
        </blockquote>
        <br/>

        <h3>More than charity.</h3>
        <p>Developing countries are home to unbelievable amounts of energy, ingenuity, and ambition. And because millions of young adults in developing countries are now online, they no longer need charities or aid organizations to tell their remarkable stories.</p>
        <p>But while they <i>may</i> have access to online social connectivity, they <i>don’t</i> have access to the resources needed to live up to all that ambition. Formal jobs are scarce. Self-employed workers make meager earnings and lack the capital needed to grow their businesses. Local banks rarely help them out.</p>
        <p>Learn about a young Kenyan lady's fruitless search for local sources of capital - and <a href="http://p2p-microlending-blog.zidisha.org/2014/03/29/spotlight-on-rose-karanja-kenya/">how Zidisha loans ultimately helped her rise to the top of her town's housing construction industry</a>.</p>
        <br/>

        <h3>Some helpful reads.</h3>
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
        <br/>

        <h3>Become a lender.</h3>
        <p>Your loan might be considered “micro” — but its effect is magnificent. Exciting, right?  Enjoy connecting with responsible, motivated people and helping them reach their goals, regardless of their location.  <a href="{{ route('lend:index') }}">Start exploring available loan opportunities.</a>
        </p>
    </div>
</div>
@stop