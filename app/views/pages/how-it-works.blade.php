@extends('layouts.master')

@section('page-title')
How It Works
@stop


@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2 info-page">
        <div class="page-header">
            <h1>How It Works</h1>
        </div>
        <p>Can “pocket change” really make a change? We’re absolutely sure of it. In fact, you’d be amazed at how far a microloan can take someone with all the motivation in the world, but little to no resources. Here at Zidisha, we not only close the gap between borrowers and lenders across the globe — we make funding and fulfilling dreams beyond easy, super satisfying and even a little bit addicting. Here’s how it all works.</p>

        <h3>1. Borrowers apply.</h3>
          <p>An entrepreneurial borrower in a developing country posts a loan request to fund his or her business.</p>
        
        <h3>2. You choose.</h3>
        <p>You pick an available loan, bid to provide all or part of the funding and set your own interest rate.</p>
        
        <h3>3. Funds flow.</h3>
        <p>After the loan is fully funded and the borrower accepts it, we disburse 100% of your loan directly to the borrower.</p>
        
        <h3>4. Everyone stays in touch.</h3>
        <p>You and the borrower can message each other back and forth to send updates about the impact of the loan.</p>
        
        <h3>5. Loans get repaid.</h3>
        <p>The borrower repays your loan in regular installments.</p>
        
        <h3>6. Funds are renewed.</h3>
        <p>You can turn right around and relend your funds to a new borrower, or withdraw them whenever you wish.</p>
        
        <h2>The Evolution of a Zidisha Loan</h2>

        <p>Start by browsing the listings of available loan opportunities <a href="{{ route('lend:index') }}">here</a>. When you find one (or more!) that you’d like to fund, you can make a bid for any portion of the loan and choose your own interest rate.</p>

        <p>Once the loan is fully funded and the borrower confirms acceptance, we disburse 100% of your funds straight to the borrower. From there on, you can communicate directly with the borrower using the Comments section located at the bottom each loan profile. (How amazing is that?)</p>

        <p>The repayment of your loan occurs in weekly or monthly installments. As soon as the borrower makes a repayment, we credit the funds back to your lending account. The repaid funds can be withdrawn at any time, or you can reinvest the money in new loans to other borrowers.</p>

        <p>Intrigued? Inspired? Undecided? You can learn even more about how Zidisha works on our <a href="{{ route('page:faq') }}">Frequently Asked Questions</a> page.</p>

        <p>If you’re ready to give it a try, head over to our <a href="{{ route('lend:index') }}">Lend</a> page. We’d be thrilled to have you join our global person-to-person microlending movement.</p>
    </div>
</div>
@stop