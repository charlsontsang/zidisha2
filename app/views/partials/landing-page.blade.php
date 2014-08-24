  <div class="row home-section info-page home text-center">
    <div class="col-md-10 col-md-offset-1">
      <h2 class="alpha">Direct loans that make dramatic impact</h2>

      <p class="lead">Zidisha is the first online microlending community that directly connects lenders and borrowers — no matter the distance or disparity between them.</p>
      <p class="lead"><a href="#"><strong>More than 14,000 people worldwide</strong></a> have started using Zidisha.</p>
    
    </div>
  </div>
</div>

<div class="container-fluid home-section home-grey home">
  <div class="container">
    <div class="row">
        <div class="col-sm-12">
          <h2 class="alpha">
            <span>Trending Projects</span>
            <span class="pull-right"><a href="{{ route('lend:index') }}">See All</a></span>
          </h2>
        </div>
        @foreach($projects as $loan)
        <div class="col-sm-4" style="padding:10px;">
            <div class="result">
                <div class="row">
                    <div class="col-xs-12">
                        <a class="pull-left profile-image" href="{{ route('loan:index', $loan->getId()) }}"
                            style="background-image:url(/assets/images/test-photos/esther.JPG)" width="100%" height="200px">
                    <!--
                            <img src="{{ $loan->getBorrower()->getUser()->getProfilePictureUrl() }}" width="100%">
                        -->
                        </a>
                    </div>
                </div>
                <div class="row">   
                    <div class="col-xs-12 loan" >
                        <div class="loan-category">
                          {{ $loan->getBorrower()->getCountry()->getName() }}
                        </div>
                        
                        <div class="lend-title">
                            <h2 class="alpha" style="height:2em;">
                                <a href="{{ route('loan:index', $loan->getId()) }}">
                                    {{ $loan->getSummary() }}
                                </a>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
  </div>
</div>

<div class="container">
  <div class="row home-section info-page home text-center">
    <div class="col-sm-12">
      <h1 class="alpha">What makes Zidisha different?</h1>
    </div>

    <div class="col-sm-4">
      <p><i class="fa fa-fw fa-dollar"></i></p>
      <h3>Lower cost for borrowers</h3>
      <p>Profits from the loans go to the borrowers and their communities, not to intermediaries.
    </div>

    <div class="col-sm-4">
      <p><i class="fa fa-fw fa-sun-o"></i></p>
      <h3>Transparency for lenders</h3>
      <p>We display the full cost of the loan and let lenders choose the interest rate.</p>
    </div>

    <div class="col-sm-4">
      <p><i class="fa fa-fw fa-comments"></i></p>
      <h3>Direct communication</h3>
      <p>Borrowers post their own loan proposals, answer questions and share updates with you directly.</p>
    </div>
    
    <div class="col-sm-12">
      <p><a href="{{ route('page:why-zidisha') }}"><strong>Learn more >></strong></a></p>
    </div>

  </div>
</div>
<div class="container-fluid home-section home-grey info-page home">
  <div class="container">
    <div class="row">
      <div class="col-sm-2 col-sm-offset-1">
        <p><a href="http://paulbuchheit.blogspot.com/2014/03/help-me-distribute-100000-to-new.html" target="_blank"><img src="assets/images/pages/press/yc_logo.png" width="100%" class="press-logo" /></a></p>
      </div>
      <div class="col-sm-8">
          
          <blockquote>
              <p>"Zidisha connects lenders directly to borrowers, providing not only an affordable loan, but also a personal connection."</p>
              <footer>
                  <cite><a href="http://paulbuchheit.blogspot.com/2014/03/help-me-distribute-100000-to-new.html" target="_blank">Gmail Creator Paul Buchheit</a></cite>
              </footer>
          </blockquote>   
      </div>
      <div class="col-sm-2 col-sm-offset-1">
        <p><a href="http://venturebeat.com/2014/03/25/meet-the-tech-for-good-startups-at-y-combinators-demo-day/" target="_blank"><img src="assets/images/pages/press/VB_logo.png" width="100%" class="press-logo" /></a></p>
      </div>
      <div class="col-sm-8">
          <blockquote>
              <p>"Zidisha is cutting out the middlemen to enable direct lending to the entrepreneurs, and only needs 10 percent in fees to cover its cost (that’s versus the 30-80 percent Kiva requires)."</p>
              <footer>
                  <cite><a href="http://venturebeat.com/2014/03/25/meet-the-tech-for-good-startups-at-y-combinators-demo-day/" target="_blank">Venture Beat</a></cite>
              </footer>
          </blockquote> 
      </div>    
      <div class="col-sm-2 col-sm-offset-1">
        <p><a href="http://techcrunch.com/2014/01/23/zidisha-launches-a-kickstarter-style-micro-lending-platform-for-low-income-entrepreneurs-in-developing-countries/" target="_blank"><img src="assets/images/pages/press/tc_logo.png" width="100%" class="press-logo" /></a></p>
      </div>
      <div class="col-sm-8">
          <blockquote>
              <p>"By enabling the efficient flow of capital across international boundaries and wealth divisions, peer-to-peer microlending has the ability to have an enormous impact — on a global scale."</p>
              <footer>
                  <cite><a href="http://techcrunch.com/2014/01/23/zidisha-launches-a-kickstarter-style-micro-lending-platform-for-low-income-entrepreneurs-in-developing-countries/" target="_blank">TechCrunch</a></cite>
              </footer>
          </blockquote> 
      </div>

      <div class="col-sm-12">
        <p class="text-center"><a href="{{ route('page:press') }}"><strong>More press >></strong></a></p>
       
      </div>
    </div>
  </div>
</div>