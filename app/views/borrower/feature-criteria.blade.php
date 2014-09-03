@extends('layouts.master')

@section('page-title')
How to have your loan featured
@stop

@section('content')
<div class="row">
    <div class="col-sm-3 col-md-4">
        <ul class="nav side-menu" role="complementary">
          <h4>Quick Links</h4>
            @include('partials.nav-links.borrower-links')       
        </ul>
    </div>

    <div class="col-sm-9 col-md-8 info-page">
        <div class="page-header">
            <h1>How to have your loan featured</h1>
        </div>

        <p>If your loan application is chosen for inclusion in the Featured category, it will be among the first applications shown to
            lenders when they visit our website. Loans that make it into the Featured category are usually funded in less than a day.</p>

        <p>Here are the criteria that we use to decide which loan applications to feature:</p>
        <br/>

        <h4>1. Repayment Record</h4>

        <p>Ensure you maintain a high on-time repayment rate score. We do not usually feature any profiles with a poor on-time repayment
            rate for past loans.</p>

        <h4>2. Photo Quality</h4>

        <p>This is one of the most important criteria, because the photo quality is what attracts potential lenders to click on your
            profile. We do not feature any profiles with poor photo quality, even if the repayment record and writing in the profile is
            very good. We are likely to feature your application if your photo meets these three criteria:</p>
        <ul>
            <li>Describes your business or area of investment. For example, if you have a store, take a photo of yourself in your store.
                If you are raising a loan for school tuition, you may choose a photo of yourself at a desk studying.
            </li>
            <li>Shows your face clearly. Make sure the photo is not too far away or too dark.</li>
            <li>Shows you smiling. Your loan application is much more likely to be funded if you smile, because lenders tend to be drawn
                first to smiling photos.
            </li>
        </ul>

        <p>You may view a wonderful example of smiling photo that describes the business <a
                href="https://www.zidisha.org/in/microfinance/loan/jesangnge/7665.html" target="blank">here</a>. This loan application was
            funded in record time because the smiling business photo attracted many lenders to click on the profile.</p>

        <h4>3. Clear Business Proposal</h4>

        <p>We do not normally feature any applications that do not explain clearly what will be bought with the loan funds and how this
            will improve your business or quality of life. "The loan will be used to expand my business" is not precise enough - you
            should instead explain what exactly you will buy and why it is needed. It is also nice if you list the prices of items in US
            Dollars, so that lenders can understand what each thing costs in a currency with which they are familiar.</p>

        <p>You may view a good example of a very clear explanation <a href="https://www.zidisha.org/microfinance/loan/samsang2/6434.html"
                                                                      target="blank">here</a>. The My Loan Proposal section describes
            clearly what will be bought, how much it will cost in dollars, and how that will benefit the business.</p>

        <h4>4. Communication with Lenders</h4>

        <p>We are much more likely to feature your profile if you have been posting updates regularly and responding to lender questions
            in the Comments section. You should post comments not only while you are fundraising, but also regularly throughout the
            lending period once you have taken out a loan. These can be business updates, or general sharing of news about life in your
            country, successes of your children, and any other thing that may be of interest to your lenders and supporters.</p>

        <p>You may view a great example of quality communication <a href="https://www.zidisha.org/microfinance/loan/mkufunzi/5719.html"
                                                                    target="blank">here</a>. On March 27, 2014, the member responded with
            prompt, helpful and interesting information to a lender question - and we featured his words in our Facebook and Twitter
            account that day.</p>

        <h4>5. Something Interesting or Memorable</h4>

        <p>We are much more likely to feature a profile that tells lenders somthing that is unique or inspiring. Think about what makes
            your own story special and include it in the "About Me" section of your profile. For example, if you taught yourself to type
            using cybercafes, or have adopted orphans, or serve as a choir leader, these are all memorable things that will help you
            connect with potential lenders.</p>

        <p>You may view a good example of an interesting profile <a href="https://www.zidisha.org/microfinance/loan/janetciru/5422.html"
                                                                    target="blank">here</a>. Note how the member has shared rich details
            about the kind of music she makes in a way that makes one want to keep reading. She even posted a link to her album and
            translation of the lyrics in the Comment section. This is the kind of memorable story that will inspire lenders to continue
            supporting this member.</p>
    </div>
</div>
@stop
