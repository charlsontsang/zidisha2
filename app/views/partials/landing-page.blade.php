<div class="container-fluid home-section home-grey home">
  <div class="container">
    <div class="row">
        <div class="col-sm-12">
          <h2 class="alpha">
            <span>Trending Projects</span>
            <span class="pull-right"><a href="{{ route('lend:index') }}" id="see-all">See All</a></span>
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

                        <div id="funding-tag">
                            <span><strong>${{ ceil($loan->getStillNeededUsdAmount()->getAmount()) }}</strong></span>
                            <br/>
                            <span class="text-light">Needed</span>
                        </div>

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
      <div class="col-sm-3">
        <p><a href="http://blogs.wsj.com/venturecapital/2014/08/25/y-combinator-nonprofit-zidisha-changes-microfinance-equation/" target="_blank"><img src="/assets/images/pages/press/wsj_logo.png" width="110%" class="press-logo" /></a></p>
      </div>
      <div class="col-sm-9">
          
          <blockquote>
              <p>"This type of lending is possible in Africa now because many people have access to the internet."</p>
              <footer>
                  <cite><a href="http://blogs.wsj.com/venturecapital/2014/08/25/y-combinator-nonprofit-zidisha-changes-microfinance-equation/" target="_blank">The Wall Street Journal</a></cite>
              </footer>
          </blockquote>   
      </div>
      <div class="col-sm-3">
        <p><a href="http://paulbuchheit.blogspot.com/2014/03/help-me-distribute-100000-to-new.html" target="_blank"><img src="/assets/images/pages/press/yc_logo.png" width="100%" class="press-logo" /></a></p>
      </div>
      <div class="col-sm-9">
          <blockquote>
              <p>"Zidisha connects lenders directly to borrowers, providing not only an affordable loan, but also a personal connection."</p>
              <footer>
                  <cite><a href="http://paulbuchheit.blogspot.com/2014/03/help-me-distribute-100000-to-new.html" target="_blank">Gmail Creator Paul Buchheit</a></cite>
              </footer>
          </blockquote>
      </div>    
      <div class="col-sm-3">
        <p><a href="http://techcrunch.com/2014/01/23/zidisha-launches-a-kickstarter-style-micro-lending-platform-for-low-income-entrepreneurs-in-developing-countries/" target="_blank"><img src="/assets/images/pages/press/tc_logo.png" width="60%" id="tc_logo" class="press-logo" /></a></p>
      </div>
      <div class="col-sm-9">
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