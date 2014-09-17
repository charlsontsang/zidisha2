@extends('layouts.master')

@section('page-title')
How It Works
@stop


@section('content')
<div class="row">
        <div class="col-sm-3 col-md-4">
            <ul class="nav side-menu" role="complementary">
              <h4>About</h4>
              @include('partials.nav-links.about-links')
            </ul>
        </div>

        <div class="col-sm-9 col-md-8 info-page how-it-works">
        <div class="page-header">
            <h1>How It Works</h1>
        </div>
        <p>Can “pocket change” really make a change?  We’re absolutely sure of it.  In fact, you’d be amazed at how far a microloan can take someone with all the motivation in the world, but little to no resources.  Here's how it all works.</p>

        <hr/>

        <img src="{{ asset('assets/images/pages/how-it-works/bineta.jpg'); }}" />

        <h4>1. Borrowers apply.</h4>

        <p>Bineta is a talented seamstress in Senegal, West Africa.  Her gorgeous dresses are in high demand - but because she sews them by hand, she produces at most one or two per week.</p>

        <hr/>

        <img src="{{ asset('assets/images/pages/how-it-works/screenshot.jpg'); }}" width="100%" />

        <h4>2. Lenders choose.</h4>
        <p>One day a friend invites Bineta to join Zidisha, and she posts a loan request to fund her dream: an electric sewing machine.</p>
        <p>Dozens of lenders around the world chip in to fund Bineta's loan.</p>
        
        <hr/>

        <img src="{{ asset('assets/images/pages/how-it-works/bineta2.jpg'); }}" width="100%" />
 
        <h4>3. Production increases.</h4>
        <p>The new sewing machine triples Bineta's production capacity. She now makes six dresses per week!</p>

        <hr/>

        <img src="{{ asset('assets/images/pages/how-it-works/employee.jpg'); }}" width="100%" />

        <h4>4. Jobs are created.</h4>
        <p>Bineta repays her lenders and raises a second loan to open a studio. She hires her first employee.</p>

        <hr/>

        <img src="{{ asset('assets/images/pages/how-it-works/child.jpg'); }}" width="100%" />

        <h4>5. Business earnings improve lives.</h4>
        <p>Bineta now comfortably supports her children with the profits from her dress sales.</p>
 
        <hr/>

        <img src="{{ asset('assets/images/pages/how-it-works/daughter.jpg'); }}" width="100%" />
 
        <h4>6. The next generation gets a head start.</h4>
        <p>Bineta uses her increased income to send her teenage daughter to a college preparatory school.</p>
      
        <hr/>

        <img src="{{ asset('assets/images/pages/how-it-works/soni.jpg'); }}" width="100%" />

        <h4>7. Loans are recycled to new entrepreneurs.</h4>
        <p>Bineta's lenders distribute the repaid funds to new entrepreneurs.  One of them is Soni in Indonesia, who uses the funds to open an after-school tutoring center in his village.  The cycle repeats!</p>
       
        <hr/>


        <h3>The Evolution of a Zidisha Loan</h3>

        <p>Start by browsing the listings of available loan opportunities <a href="{{ route('lend:index') }}">here</a>. When you find one (or more!) that you’d like to fund, you can make a bid for any portion of the loan and choose your own interest rate.</p>

        <p>Once the loan is fully funded and the borrower confirms acceptance, we disburse 100% of your funds straight to the borrower. From there on, you can communicate directly with the borrower using the Comments section located at the bottom each loan profile. (How amazing is that?)</p>

        <p>The repayment of your loan occurs in weekly or monthly installments. As soon as the borrower makes a repayment, we credit the funds back to your lending account. The repaid funds can be withdrawn at any time, or you can reinvest the money in new loans to other borrowers.</p>

        <p>Intrigued? Inspired? Undecided? You can learn even more about how Zidisha works on our <a href="{{ route('page:faq') }}">Frequently Asked Questions</a> page.</p>

        <p>If you’re ready to give it a try, head over to our <a href="{{ route('lend:index') }}">Lend</a> page. We’d be thrilled to have you join our global person-to-person microlending movement.</p>
    </div>
</div>
@stop